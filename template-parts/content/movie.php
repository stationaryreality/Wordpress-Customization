<?php
$movie_id  = get_the_ID();
$cover     = get_field('cover_image');
$img_url   = $cover ? $cover['sizes']['medium'] : '';
$wiki_slug = get_field('wikipedia_slug');
?>

<!-- CRITICAL: Keep .person-content to prevent the theme footer from snapping up -->
<div class="person-content cpt-movie-content">

  <?php get_template_part('template-parts/navigation/movie'); ?>

  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="cpt-movie-cover">
  <?php endif; ?>

  <h1 class="cpt-movie-title"><?php the_title(); ?></h1>

  <div class="person-bio cpt-movie-bio">
    <?php
    $wiki_intro = $wiki_slug ? kp_get_wikipedia_intro($wiki_slug) : '';
    echo $wiki_intro ? wp_kses_post($wiki_intro) : wp_kses_post(get_the_content());
    ?>
  </div>

  <?php
  // === Related Quotes ===
  $quotes = get_posts([
    'post_type'      => 'quote',
    'posts_per_page' => -1,
    'meta_query'     => [
      [
        'key'     => 'quote_source',
        'value'   => $movie_id,
        'compare' => '='
      ]
    ]
  ]);
  if (!empty($quotes)) {
    get_template_part('template-parts/render/content-objects', null, ['posts' => $quotes, 'title' => 'Quotes']);
  }

  // === Related Excerpts ===
  $excerpts = get_posts([
    'post_type'      => 'excerpt',
    'posts_per_page' => -1,
    'meta_query'     => [
      [
        'key'     => 'excerpt_source',
        'value'   => $movie_id,
        'compare' => '='
      ]
    ]
  ]);
  if (!empty($excerpts)) {
    get_template_part('template-parts/render/content-objects', null, ['posts' => $excerpts, 'title' => 'Excerpts']);
  }

  show_featured_in_threads('movies_referenced');
  ?>

</div>