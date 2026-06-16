<?php
$game_id  = get_the_ID();
$summary  = get_field('summary');
$cover    = get_field('cover_image');
$img_url  = $cover ? $cover['sizes']['medium'] : '';
$wiki_slug = get_field('wikipedia_slug');

?>

<div class="game-content" style="text-align:center;">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" style="display:block;margin:0 auto;">
  <?php endif; ?>

  <h1><?php the_title(); ?></h1>
  <?php the_content(); ?>

<div class="game-description">
    <?php
    $wiki_intro = $wiki_slug ? kp_get_wikipedia_intro($wiki_slug) : false;
    echo $wiki_intro ?: get_the_content();
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

if ($quotes) {
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

if ($excerpts) {
    get_template_part('template-parts/render/content-objects', null, ['posts' => $excerpts, 'title' => 'Excerpts']);
}
?>

  <?php show_featured_in_threads('games_referenced'); ?>

  <?php get_template_part('content/game-nav'); ?>
</div>