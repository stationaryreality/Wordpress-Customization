<?php
$org_id    = get_the_ID();
$bio       = get_field('org_bio', $org_id);
$logo      = get_field('cover_image', $org_id);
$img_url   = $logo ? $logo['sizes']['thumbnail'] : '';
$wiki_slug = get_field('wikipedia_slug', $org_id);
$people    = get_field('related_people', $org_id); // ACF relationship or repeater field

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

<div class="organization-header" style="text-align:center;">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="author-thumbnail" style="border-radius:0; aspect-ratio:1/1; object-fit:cover; max-width:300px; margin-bottom:1em;">
  <?php endif; ?>
  <h1><?php the_title(); ?></h1>
</div>

<div class="organization-bio" style="text-align:center;">
  <?php if ($bio): ?>
    <?php echo wp_kses_post($bio); ?>
  <?php elseif ($wiki_slug): ?>
    <p><?php echo get_wikipedia_intro($wiki_slug); ?></p>
  <?php else: ?>
    <?php the_content(); ?>
  <?php endif; ?>
</div>

<?php if ($people): ?>
  <div class="related-people" style="margin-top:3em; text-align:center;">
    <h2>Related People</h2>
    <ul style="list-style:none; padding:0; display:inline-block; text-align:left;">
      <?php foreach ($people as $person): ?>
        <li>
          <a href="<?php echo get_permalink($person->ID); ?>">
            <?php echo esc_html(get_the_title($person->ID)); ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php
// === Chapters Featuring This Organization ===
$threads = get_posts([
  'post_type'      => 'chapter',
  'posts_per_page' => -1,
  'orderby'        => 'menu_order',
  'order'          => 'ASC',
  'meta_query'     => [
    [
      'key'     => 'organizations_referenced',
      'value'   => '"' . $org_id . '"',
      'compare' => 'LIKE'
    ]
  ]
]);

if ($threads): ?>
  <div class="narrative-threads" style="margin-top:4em; text-align:center;">
    <h2>Featured In</h2>
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

<?php get_template_part('content/organization-nav'); ?>
