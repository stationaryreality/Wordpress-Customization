<?php
$game_id   = get_the_ID();
$summary   = get_field('summary');
$cover     = get_field('cover_image');
$img_url   = $cover ? $cover['sizes']['medium'] : '';
$wiki_slug = get_field('wikipedia_slug');
?>

<div class="person-content">   <!-- consistent wrapper -->

  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="game-cover" style="display:block;margin:0 auto;max-width:300px;">
  <?php endif; ?>

  <h1><?php the_title(); ?></h1>

  <div class="person-bio">     <!-- matches artist/song bio class -->
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
        'value'   => $game_id,
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
        'value'   => $game_id,
        'compare' => '='
      ]
    ]
  ]);

  if (!empty($excerpts)) {
    get_template_part('template-parts/render/content-objects', null, ['posts' => $excerpts, 'title' => 'Excerpts']);
  }

  // === Featured in threads (custom function) ===
  show_featured_in_threads('games_referenced');
  ?>

  <?php get_template_part('content/game-nav'); ?>

</div>   <!-- end person-content -->