<?php
$image_id   = get_the_ID();
$caption    = get_field('image_caption', $image_id);
$cover      = get_field('cover_image', $image_id);
$img_url    = $cover ? $cover['sizes']['thumbnail'] : '';
$wiki_slug  = get_field('wikipedia_slug', $image_id);

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

<div class="image-header" style="text-align:center;">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="author-thumbnail" style="border-radius:0; aspect-ratio:1/1; object-fit:cover; max-width:300px; margin-bottom:1em;">
  <?php endif; ?>
  <h1><?php the_title(); ?></h1>
</div>

<div class="image-caption">
  <?php if ($caption): ?>
    <?php echo wp_kses_post($caption); ?>
  <?php elseif ($wiki_slug): ?>
    <p><?php echo get_wikipedia_intro($wiki_slug); ?></p>
  <?php else: ?>
    <?php the_content(); ?>
  <?php endif; ?>
</div>

<?php get_template_part('content/image-nav'); ?>
