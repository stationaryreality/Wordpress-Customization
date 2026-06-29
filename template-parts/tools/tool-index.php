<?php

$post_types = [
 'artist','profile','book','concept','movie','quote','lyric',
 'organization','image','song','chapter','video',
 'excerpt','fragment','element','show','game', 'portal'
];

$entries = [];
$type_counts = [];

/* ===== POSTS ===== */

$q = new WP_Query([
  'post_type'      => $post_types,
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
  'post_status'    => 'publish'
]);

while ($q->have_posts()) {

  $q->the_post();

  $type = get_post_type();

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

wp_reset_postdata();

/* ===== TAXONOMIES ===== */

foreach (['theme', 'topic'] as $tax) {

  $terms = get_terms([
    'taxonomy'   => $tax,
    'hide_empty' => false
  ]);

  foreach ($terms as $t) {

    $meta  = get_cpt_metadata($tax);
    $emoji = $meta['emoji'] ?? '•';

    $entries[] = [
      'title' => $t->name,
      'url'   => get_term_link($t),
      'emoji' => $emoji,
      'type'  => $tax
    ];

    if (!isset($type_counts[$tax])) {
      $type_counts[$tax] = 0;
    }

    $type_counts[$tax]++;
  }
}

/* ===== SORT ===== */

usort($entries, fn($a, $b) => strcasecmp($a['title'], $b['title']));

ksort($type_counts);

$total_count = count($entries);

?>

<section class="tool-index cpt-index-clean">

<header class="tool-header">

<h2>Alphabetical Index</h2>

<p class="cpt-total" style="margin:0.75em 0 1.5em 0;">
<?php echo number_format($total_count); ?> total entries
</p>

<p style="margin:1.5em 0;">
<a href="?tool=newest">→ View Newest Content</a>
</p>

</header>

<!-- ===== FILTERS ===== -->

<div class="cpt-filters" style="margin-bottom:2em;">

<button onclick="selectAll(true)">Select All</button>
<button onclick="selectAll(false)">Deselect All</button>

<div style="margin-top:1em; display:flex; flex-wrap:wrap; gap:12px;">

<?php foreach ($type_counts as $type => $count): ?>

<label>

<input type="checkbox"
       value="<?php echo esc_attr($type); ?>"
       checked>

<?php echo ucfirst($type); ?>
(<?php echo $count; ?>)

</label>

<?php endforeach; ?>

</div>
</div>

<!-- ===== LIST ===== -->

<ul class="cpt-clean-list">

<?php foreach ($entries as $e): ?>

<li data-type="<?php echo esc_attr($e['type']); ?>">

<span class="entry-emoji">
<?php echo $e['emoji']; ?>
</span>

<a href="<?php echo esc_url($e['url']); ?>"
   target="_blank"
   rel="noopener">

<?php echo esc_html($e['title']); ?>

</a>

</li>

<?php endforeach; ?>

</ul>

</section>

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

      item.style.display = active.includes(type)
        ? ''
        : 'none';

    });
  }

  checkboxes.forEach(cb => {
    cb.addEventListener('change', filterList);
  });

  window.selectAll = function(state) {

    checkboxes.forEach(cb => {
      cb.checked = state;
    });

    filterList();
  };

});
</script>