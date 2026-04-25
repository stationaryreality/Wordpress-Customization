<?php
/* Template Name: Admin Tools – Orphans */
get_header();

/* ===== CONFIG ===== */

$excluded_types = ['chapter','fragment','element'];

$all_types = [
 'artist','profile','book','concept','movie','quote','lyric',
 'reference','organization','image','song','excerpt','show'
];

/* Relationship fields (shared across chapter/fragment/element) */
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

/* ===== STEP 1: COLLECT ALL REFERENCED IDS ===== */

$referenced_ids = [];

/* Get all chapter/fragment/element posts */
$containers = new WP_Query([
  'post_type' => $excluded_types,
  'posts_per_page' => -1,
  'post_status' => 'publish'
]);

while ($containers->have_posts()) {
  $containers->the_post();
  $id = get_the_ID();

  /* Loop relationship fields */
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

  /* Special case: chapter_songs repeater */
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

/* Deduplicate */
$referenced_ids = array_unique($referenced_ids);

/* ===== STEP 2: GET ALL CONTENT ===== */

$q = new WP_Query([
  'post_type' => $all_types,
  'posts_per_page' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
  'post_status' => 'publish'
]);

$entries = [];

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
      'emoji' => $emoji
    ];
  }
}

wp_reset_postdata();

/* ===== SORT ===== */

usort($entries, fn($a,$b)=>strcasecmp($a['title'],$b['title']));

$total_count = count($entries);
?>

<main class="cpt-index-clean">

<header class="archive-header">
<h1><?php the_title(); ?></h1>

<p class="cpt-total" style="margin:0.75em 0 1.5em 0;">
<?php echo number_format($total_count); ?> Custom Post Types (CPTs) That are orphaned
</p>

<p style="margin:1.5em 0;">
<a href="/site-index/">← View Full Index</a>
</p>

</header>

<ul class="cpt-clean-list">
<?php foreach ($entries as $e): ?>
<li>
<span class="entry-emoji"><?php echo $e['emoji']; ?></span>
<a href="<?php echo esc_url($e['url']); ?>">
<?php echo esc_html($e['title']); ?>
</a>
</li>
<?php endforeach; ?>
</ul>

</main>

<?php get_footer(); ?>