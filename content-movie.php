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

  <?php
    // === Related Quotes (from quote CPT) ===
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

    if ($quotes): ?>
      <div class="related-quotes" style="margin-top:3em; text-align:center;">
        <h2>Quotes</h2>
        <ul style="list-style:none; padding:0; display:inline-block; text-align:center;">
          <?php foreach ($quotes as $quote): ?>
            <li>
              <a href="<?php echo get_permalink($quote->ID); ?>">
                <?php echo esc_html(get_the_title($quote->ID)); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
  <?php endif; ?>


  <?php
    // === Related Excerpts (from excerpt CPT) ===
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

    if ($excerpts): ?>
      <div class="related-excerpts" style="margin-top:3em; text-align:center;">
        <h2>Excerpts</h2>
        <ul style="list-style:none; padding:0; display:inline-block; text-align:center;">
          <?php foreach ($excerpts as $excerpt): ?>
            <li>
              <a href="<?php echo get_permalink($excerpt->ID); ?>">
                <?php echo esc_html(get_the_title($excerpt->ID)); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
  <?php endif; ?>

  <?php show_featured_in_threads('movies_referenced'); ?>

  <?php get_template_part('content/movie-nav'); ?>
</div>
