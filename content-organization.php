<?php
$org_id    = get_the_ID();
$bio       = get_field('org_bio', $org_id);
$logo      = get_field('cover_image', $org_id);
$img_url   = $logo ? $logo['sizes']['thumbnail'] : '';
$wiki_slug = get_field('wikipedia_slug', $org_id);

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

<div class="song-header" style="text-align:center;">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="author-thumbnail" style="border-radius:0; aspect-ratio:1/1; object-fit:cover; max-width:300px; margin-bottom:1em;">
  <?php endif; ?>
  <h1><?php the_title(); ?></h1>
</div>

<div class="song-bio">
  <?php if ($bio): ?>
    <?php echo wp_kses_post($bio); ?>
  <?php elseif ($wiki_slug): ?>
    <p><?php echo get_wikipedia_intro($wiki_slug); ?></p>
  <?php else: ?>
    <?php the_content(); ?>
  <?php endif; ?>
</div>

<?php
// === Chapters Featuring This Organization ===
$chapters = get_field('related_chapters', $org_id);
if (!empty($chapters)): ?>
  <div class="narrative-threads">
    <h2>Narrative Threads Featuring This Organization</h2>
    <div class="thread-grid">
      <?php foreach ($chapters as $chapter):
        $thumb = get_the_post_thumbnail_url($chapter, 'medium');
        ?>
        <div class="thread-item">
          <a href="<?php echo get_permalink($chapter); ?>">
            <?php if ($thumb): ?>
              <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($chapter)); ?>">
            <?php endif; ?>
            <h3><?php echo esc_html(get_the_title($chapter)); ?></h3>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<?php get_template_part('content/organization-nav'); ?>
