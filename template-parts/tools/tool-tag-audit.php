<?php

/* ===== CONFIG ===== */

$all_types = [
 'profile','book','movie','quote','lyric',
 'reference','organization','image','song',
 'excerpt','show','game'
];

$mode = $_GET['mode'] ?? 'missing-both';

$valid_modes = [
  'all',
  'missing-theme',
  'missing-topic',
  'missing-both'
];

if (!in_array($mode, $valid_modes)) {
  $mode = 'missing-both';
}

/* ===== QUERY ===== */

$q = new WP_Query([
  'post_type' => $all_types,
  'posts_per_page' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
  'post_status' => 'publish'
]);

$entries = [];
$type_counts = [];

/* ===== LOOP ===== */

while ($q->have_posts()) {

  $q->the_post();

  $id   = get_the_ID();
  $type = get_post_type();

  $themes = get_the_terms($id, 'theme');
  $topics = get_the_terms($id, 'topic');

  $has_theme = !empty($themes) && !is_wp_error($themes);
  $has_topic = !empty($topics) && !is_wp_error($topics);

  $include = false;

  switch ($mode) {

    case 'all':
      $include = true;
      break;

    case 'missing-theme':
      $include = !$has_theme;
      break;

    case 'missing-topic':
      $include = !$has_topic;
      break;

    case 'missing-both':
      $include = !$has_theme && !$has_topic;
      break;
  }

  if ($include) {

    $meta  = get_cpt_metadata($type);
    $emoji = $meta['emoji'] ?? '•';

    $entries[] = [
      'title' => get_the_title(),
      'url'   => get_permalink(),
      'emoji' => $emoji,
      'type'  => $type,
      'theme' => $has_theme,
      'topic' => $has_topic
    ];

    if (!isset($type_counts[$type])) {
      $type_counts[$type] = 0;
    }

    $type_counts[$type]++;
  }
}

wp_reset_postdata();

/* ===== SORT ===== */

usort($entries, fn($a,$b)=>strcasecmp($a['title'],$b['title']));
ksort($type_counts);

$total_count = count($entries);
?>

<section class="admin-tool-section">

<h2>Tag Audit</h2>

<p style="margin:0.75em 0 1.5em 0;">
<?php echo number_format($total_count); ?> matching entries
</p>

<div style="margin-bottom:1.5em; display:flex; gap:12px; flex-wrap:wrap;">

<a href="?tool=tag-audit&mode=missing-both">Missing Both</a>

<a href="?tool=tag-audit&mode=missing-theme">Missing Theme</a>

<a href="?tool=tag-audit&mode=missing-topic">Missing Topic</a>

<a href="?tool=tag-audit&mode=all">Show All</a>

</div>

<?php get_template_part('template-parts/tools/tool', 'filters', [
  'type_counts' => $type_counts
]); ?>

<ul class="cpt-clean-list">

<?php foreach ($entries as $e): ?>

<li data-type="<?php echo esc_attr($e['type']); ?>">

<span class="entry-emoji">
<?php echo $e['emoji']; ?>
</span>

<a href="<?php echo esc_url($e['url']); ?>" target="_blank" rel="noopener">
<?php echo esc_html($e['title']); ?>
</a>

<span style="margin-left:10px; opacity:0.7; font-size:0.9em;">

Theme:
<?php echo $e['theme'] ? '✅' : '❌'; ?>

&nbsp;&nbsp;

Topic:
<?php echo $e['topic'] ? '✅' : '❌'; ?>

</span>

</li>

<?php endforeach; ?>

</ul>

</section>