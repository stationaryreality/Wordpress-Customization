<?php
$bio = get_field('bio'); // Optional ACF field
$portrait = get_field('portrait_image', get_the_ID());
$img_url = $portrait ? $portrait['sizes']['thumbnail'] : '';
$wiki_slug = get_field('wikipedia_slug');

// Function to fetch Wikipedia summary
function get_wikipedia_intro($slug) {
    $api_url = "https://en.wikipedia.org/api/rest_v1/page/summary/" . urlencode($slug);

    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) return false;

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!empty($data['extract'])) {
        return esc_html($data['extract']);
    }

    return false;
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
  
  <?php get_template_part('content/artist-nav'); ?>
</div>
