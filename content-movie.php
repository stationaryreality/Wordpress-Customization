<?php
$movie_id  = get_the_ID();
$director  = get_field('director');
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

  <?php if ($director): ?>
    <p><strong><?php echo esc_html($director); ?></strong></p>
  <?php endif; ?>

  <div class="movie-description">
    <?php
      $wiki_intro = $wiki_slug ? get_wikipedia_intro($wiki_slug) : false;
      echo $wiki_intro ?: get_the_content();
    ?>
  </div>

  <?php
  // === Narrative Threads referencing this movie ===
  $threads = get_posts([
    'post_type'      => 'chapter',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'meta_query'     => [
      [
        'key'     => 'movies_referenced',
        'value'   => '"' . $movie_id . '"', // ensure exact match inside serialized array
        'compare' => 'LIKE'
      ]
    ]
  ]);

  if ($threads): ?>
    <div class="narrative-threads">
      <h2>Narrative Threads</h2>
      <div class="thread-grid">
        <?php foreach ($threads as $thread):
          $thumb = get_the_post_thumbnail_url($thread->ID, 'medium');
        ?>
          <div class="thread-item">
            <a href="<?php echo get_permalink($thread->ID); ?>">
              <?php if ($thumb): ?>
                <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($thread->ID)); ?>">
              <?php endif; ?>
              <h3><?php echo esc_html(get_the_title($thread->ID)); ?></h3>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <?php get_template_part('content/movie-nav'); ?>
</div>
