<?php
/* Template Name: All CPT Index (Alphabetical) */
get_header(); 

$icons = [
  'artist'   => '🎹',
  'profile'  => '👤',
  'book'     => '📚',
  'concept'  => '🔎',
  'movie'    => '🎬',
  'quote'    => '💬',
];

// Query all relevant CPTs
$args = [
  'post_type'      => array_keys($icons),
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
  'post_status'    => 'publish'
];
$query = new WP_Query($args);
?>

<main class="cpt-index-alphabetical">
  <header class="archive-header">
    <h1 class="post-title">All Entries (Alphabetical Index)</h1>
    <p class="post-subtitle">Browse every referenced person, idea, quote, or source in one unified list.</p>
  </header>

  <?php if ($query->have_posts()) : ?>
    <ul class="cpt-alpha-list">
      <?php while ($query->have_posts()) : $query->the_post();
        $type = get_post_type();
        $icon = $icons[$type] ?? '❓';
      ?>
        <li>
          <span class="cpt-icon"><?php echo $icon; ?></span>
          <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </li>
      <?php endwhile; ?>
    </ul>
    <?php wp_reset_postdata(); ?>
  <?php else : ?>
    <p>No entries found.</p>
  <?php endif; ?>
</main>

<?php get_footer(); ?>
