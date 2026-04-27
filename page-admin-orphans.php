<?php
/* Template Name: Admin Tools – Orphans */
get_header();

/* ===== CONFIG ===== */

$excluded_types = ['chapter','fragment','element'];

$all_types = [
 'profile','book','movie','quote','lyric',
 'reference','organization','image','song','excerpt','show'
];

/* Relationship fields */
$relationship_fields = [
 'books_cited',
 'concepts_referenced',
 'excerpts_referenced',
 'lyrics_referenced',
 'movies_referenced',
 'shows_referenced',
 'organizations_referenced',
 'people_referenced',
 'quotes_referenced',
 'images_linked',
 'chapter_references'
];

/* ===== STEP 1: COLLECT REFERENCED IDS ===== */

$referenced_ids = [];

$containers = new WP_Query([
  'post_type' => $excluded_types,
  'posts_per_page' => -1,
  'post_status' => 'publish'
]);

while ($containers->have_posts()) {
  $containers->the_post();
  $id = get_the_ID();

  foreach ($relationship_fields as $field) {
    $items = get_field($field, $id);

    if ($items) {
      foreach ($items as $item) {
        if (is_object($item)) {
          $referenced_ids[] = $item->ID;
        }
      }
    }
  }

  /* chapter_songs repeater */
  $songs = get_field('chapter_songs', $id);
  if ($songs) {
    foreach ($songs as $row) {
      if (!empty($row['song']) && is_object($row['song'])) {
        $referenced_ids[] = $row['song']->ID;
      }
    }
  }
}

wp_reset_postdata();

$referenced_ids = array_unique($referenced_ids);

/* ===== STEP 2: GET ALL POSTS ===== */

$q = new WP_Query([
  'post_type' => $all_types,
  'posts_per_page' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
  'post_status' => 'publish'
]);

$entries = [];
$type_counts = [];

/* ===== STEP 3: FILTER ORPHANS ===== */

while ($q->have_posts()) {
  $q->the_post();

  $id   = get_the_ID();
  $type = get_post_type();

  if (!in_array($id, $referenced_ids)) {

    $meta  = get_cpt_metadata($type);
    $emoji = $meta['emoji'] ?? '•';

    $entries[] = [
      'title' => get_the_title(),
      'url'   => get_permalink(),
      'emoji' => $emoji,
      'type'  => $type
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

<main class="cpt-index-clean">

<header class="archive-header">
<h1><?php the_title(); ?></h1>

<p class="cpt-total" style="margin:0.75em 0 1.5em 0;">
<?php echo number_format($total_count); ?> orphaned entries
</p>

<p style="margin:1.5em 0;">
<a href="/cpt-index/">← View Full Index</a>
</p>
</header>

<!-- ===== FILTER UI ===== -->

<div class="cpt-filters" style="margin-bottom:2em;">

<button onclick="selectAll(true)">Select All</button>
<button onclick="selectAll(false)">Deselect All</button>

<div style="margin-top:1em; display:flex; flex-wrap:wrap; gap:12px;">

<?php foreach ($type_counts as $type => $count): ?>
<label>
<input type="checkbox" value="<?php echo esc_attr($type); ?>" checked>
<?php echo ucfirst($type); ?> (<?php echo $count; ?>)
</label>
<?php endforeach; ?>

</div>
</div>

<!-- ===== LIST ===== -->

<ul class="cpt-clean-list">
<?php foreach ($entries as $e): ?>
<li data-type="<?php echo esc_attr($e['type']); ?>">
<span class="entry-emoji"><?php echo $e['emoji']; ?></span>
<a href="<?php echo esc_url($e['url']); ?>">
<?php echo esc_html($e['title']); ?>
</a>
</li>
<?php endforeach; ?>
</ul>

</main>

<!-- ===== JS FILTER ===== -->

<script>
document.addEventListener('DOMContentLoaded', function() {

  const checkboxes = document.querySelectorAll('.cpt-filters input[type="checkbox"]');
  const items = document.querySelectorAll('.cpt-clean-list li');

  function filterList() {
    const active = Array.from(checkboxes)
      .filter(cb => cb.checked)
      .map(cb => cb.value);

    items.forEach(item => {
      const type = item.getAttribute('data-type');
      item.style.display = active.includes(type) ? '' : 'none';
    });
  }

  checkboxes.forEach(cb => {
    cb.addEventListener('change', filterList);
  });

  window.selectAll = function(state) {
    checkboxes.forEach(cb => cb.checked = state);
    filterList();
  };

});
</script>

<?php get_footer(); ?>