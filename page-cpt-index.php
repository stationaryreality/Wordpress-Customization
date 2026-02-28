<?php
/* Template Name: CPT Index (Alphabetical Clean) */
get_header();

$post_types = [
 'artist','profile','book','concept','movie','quote','lyric',
 'reference','organization','image','song','chapter',
 'excerpt','fragment','element','show'
];

$q = new WP_Query([
  'post_type'=>$post_types,
  'posts_per_page'=>-1,
  'orderby'=>'title',
  'order'=>'ASC',
  'post_status'=>'publish'
]);

$entries = [];

/* ===== POSTS ===== */
while ($q->have_posts()) {
  $q->the_post();
  $type = get_post_type();

  $meta  = get_cpt_metadata($type);
  $emoji = $meta['emoji'] ?? '•';

  $entries[] = [
    'title'=>get_the_title(),
    'url'=>get_permalink(),
    'emoji'=>$emoji
  ];
}
wp_reset_postdata();

/* ===== TAXONOMIES ===== */
foreach (['theme','topic'] as $tax) {
  $terms = get_terms([
    'taxonomy'=>$tax,
    'hide_empty'=>false
  ]);

  foreach ($terms as $t) {
    $meta  = get_cpt_metadata($tax);
    $emoji = $meta['emoji'] ?? '•';

    $entries[] = [
      'title'=>$t->name,
      'url'=>get_term_link($t),
      'emoji'=>$emoji
    ];
  }
}

/* ===== SORT ===== */
usort($entries, fn($a,$b)=>strcasecmp($a['title'],$b['title']));

/* ===== TOTAL ===== */
$total_count = count($entries);
?>

<main class="cpt-index-clean">

<header class="archive-header">
<h1><?php the_title(); ?></h1>

<p class="cpt-total" style="margin:0.75em 0 1.5em 0;">
<?php echo number_format($total_count); ?> total entries
</p>

<p style="margin:1.5em 0;">
<a href="/newest-content/">← View Newest Content</a>
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