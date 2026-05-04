<?php
/* Template Name: Recent Additions (Minimal) */
get_header();

/* ===== CONFIG ===== */

$post_types = [
 'artist','profile','book','concept','movie','quote','lyric',
 'reference','organization','image','song','chapter',
 'excerpt','fragment','element','show', 'game'
];

$limit = 400;

/* ===== QUERY ===== */

$q = new WP_Query([
  'post_type'      => $post_types,
  'posts_per_page' => $limit,
  'orderby'        => 'date',
  'order'          => 'DESC',
  'post_status'    => 'publish'
]);

$posts = $q->posts;

/* ===== GROUP BY MONTH ===== */

$grouped = [];

foreach ($posts as $p) {
  $month = date('F Y', strtotime($p->post_date));
  $grouped[$month][] = $p;
}
?>

<main class="cpt-index-clean">

<header class="archive-header">
  <h1><?php the_title(); ?></h1>

  <p style="margin:0.75em 0 1.5em 0;">
    Showing latest <?php echo $limit; ?> additions
  </p>

  <p style="margin:1.5em 0;">
    <a href="/site-index/">← View Alphabetical Index</a>
  </p>
</header>

<?php foreach ($grouped as $month => $items): ?>

  <h2 style="margin-top:2em;"><?php echo esc_html($month); ?></h2>

  <ul class="cpt-clean-list">

  <?php foreach ($items as $post_obj):

    $post_id = $post_obj->ID;
    $type = get_post_type($post_id);

    $meta  = get_cpt_metadata($type);
    $emoji = $meta['emoji'] ?? '•';

    $date = get_the_date('Y-m-d', $post_id);

  ?>

    <li>
      <span class="entry-emoji"><?php echo esc_html($emoji); ?></span>

      <a href="<?php echo esc_url(get_permalink($post_id)); ?>" target="_blank" rel="noopener">
        <?php echo esc_html(get_the_title($post_id)); ?>
      </a>

      <span class="entry-date" style="margin-left:10px; opacity:0.6;">
        <?php echo esc_html($date); ?>
      </span>
    </li>

  <?php endforeach; ?>

  </ul>

<?php endforeach; ?>

</main>

<?php get_footer(); ?>