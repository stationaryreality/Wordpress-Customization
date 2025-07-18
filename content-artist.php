<?php
$artist_id  = get_the_ID();
$bio        = get_field('bio', $artist_id);
$portrait   = get_field('portrait_image', $artist_id);
$img_url    = $portrait ? $portrait['sizes']['thumbnail'] : '';
$wiki_slug  = get_field('wikipedia_slug', $artist_id);

// Wikipedia summary
function get_wikipedia_intro($slug) {
  $api_url = "https://en.wikipedia.org/api/rest_v1/page/summary/" . urlencode($slug);
  $response = wp_remote_get($api_url);
  if (is_wp_error($response)) return false;
  $body = wp_remote_retrieve_body($response);
  $data = json_decode($body, true);
  return !empty($data['extract']) ? esc_html($data['extract']) : false;
}
?>

<div class="person-content">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="author-thumbnail">
  <?php endif; ?>

  <h1><?php the_title(); ?></h1>

  <div class="person-bio">
    <?php if ($bio): ?>
      <?php echo wp_kses_post($bio); ?>
    <?php elseif ($wiki_slug): ?>
      <p><?php echo get_wikipedia_intro($wiki_slug); ?></p>
    <?php else: ?>
      <?php the_content(); ?>
    <?php endif; ?>
  </div>

  <?php
  // === Narrative Threads ===
$threads = get_posts([
  'post_type'      => 'chapter',
  'posts_per_page' => -1,
  'orderby'        => 'menu_order',
  'order'          => 'ASC',
  'meta_query'     => [
    [
      'key'     => 'primary_artist',
      'value'   => $artist_id,
      'compare' => '='
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


  <?php
  // === Song Excerpts ===
  $lyrics = get_posts([
    'post_type'      => 'lyric',
    'posts_per_page' => -1,
    'meta_query'     => [
      [
        'key'     => 'artist',
        'value'   => $artist_id,
        'compare' => '='
      ]
    ]
  ]);

  if ($lyrics): ?>
    <div class="artist-lyrics">
      <h2>Song Excerpts</h2>
      <?php foreach ($lyrics as $lyric):
        $html = get_field('lyric_html_block', $lyric->ID);
        $text = get_field('quote_text', $lyric->ID);
        if ($html) {
          echo $html;
        } elseif ($text) {
          echo '<div class="plain-lyric"><blockquote>' . esc_html($text) . '</blockquote></div>';
        }
      endforeach; ?>
    </div>
  <?php endif; ?>

  <?php get_template_part('content/artist-nav'); ?>
</div>
