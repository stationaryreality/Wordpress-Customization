<?php
$movie_id  = get_the_ID();
$summary   = get_field('summary');
$cover     = get_field('cover_image');
$img_url   = $cover ? $cover['sizes']['medium'] : '';
$wiki_slug = get_field('wikipedia_slug');

function get_wikipedia_intro($slug) {
    $api_url = "https://en.wikipedia.org/api/rest_v1/page/summary/" . urlencode($slug);
    $response = wp_remote_get($api_url);
    if (is_wp_error($response)) return false;
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    return !empty($data['extract']) ? esc_html($data['extract']) : false;
}
?>

<div class="movie-content" style="text-align:center;">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" style="display:block;margin:0 auto;">
  <?php endif; ?>

  <h1><?php the_title(); ?></h1>
  <?php the_content(); ?>

  <div class="movie-description">
    <?php
      $wiki_intro = $wiki_slug ? get_wikipedia_intro($wiki_slug) : false;
      echo $wiki_intro ?: get_the_content();
    ?>
  </div>

  <?php show_featured_in_threads('movies_referenced'); ?>

  <?php get_template_part('content/movie-nav'); ?>
</div>
