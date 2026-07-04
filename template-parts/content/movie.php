<?php
$movie_id  = get_the_ID();
$summary   = get_field('summary');
$cover     = get_field('cover_image');
$img_url   = $cover ? $cover['sizes']['medium'] : '';
$wiki_slug = get_field('wikipedia_slug');
?>

<div class="person-content">

  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="movie-cover" style="display:block;margin:0 auto;max-width:300px;">
  <?php endif; ?>

  <h1><?php the_title(); ?></h1>

  <div class="person-bio">
    <?php
    // Use Wikipedia intro if available, otherwise the post content
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

  // === Featured in threads (custom function) ===
  show_featured_in_threads('movies_referenced');
  ?>

  <?php get_template_part('content/movie-nav'); ?>

</div>   <!-- end person-content -->