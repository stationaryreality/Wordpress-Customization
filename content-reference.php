<?php
$reference_id = get_the_ID();

$description  = get_field('description');
$image        = get_field('cover_image');
$img_url      = $image ? $image['sizes']['thumbnail'] : '';

$source_name  = get_field('source_name');
$credit_name  = get_field('credit_name');
$url          = get_field('url');
$archive_url  = get_field('archive_link');
$wiki_slug    = get_field('reference_wiki');

// Wikipedia summary fetcher
function get_reference_wikipedia_intro($slug) {
  $api_url = "https://en.wikipedia.org/api/rest_v1/page/summary/" . urlencode($slug);
  $response = wp_remote_get($api_url);
  if (is_wp_error($response)) return false;
  $body = wp_remote_retrieve_body($response);
  $data = json_decode($body, true);
  return !empty($data['extract']) ? esc_html($data['extract']) : false;
}
?>

<div class="reference-content" style="text-align:center;">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="reference-thumbnail">
  <?php endif; ?>

  <h1><?php the_title(); ?></h1>

  <?php if ($source_name): ?>
    <p class="reference-source" style="font-style: italic; color: #666;">
      Source: <?php echo esc_html($source_name); ?>
    </p>
  <?php endif; ?>

  <?php if ($credit_name): ?>
    <p class="reference-credit"><strong>Credit:</strong> <?php echo esc_html($credit_name); ?></p>
  <?php endif; ?>

  <?php if ($description): ?>
    <div class="reference-description">
      <?php echo wp_kses_post(wpautop($description)); ?>
    </div>
  <?php else: ?>
    <div class="reference-content-fallback">
      <?php the_content(); ?>
    </div>
  <?php endif; ?>

  <?php if ($wiki_slug): ?>
    <?php $wiki_intro = get_reference_wikipedia_intro($wiki_slug); ?>
    <?php if ($wiki_intro): ?>
      <div class="reference-wiki" style="margin-top:1.5em;">
        <h3>Wikipedia</h3>
        <p><?php echo esc_html($wiki_intro); ?></p>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <?php if ($url || $archive_url): ?>
    <div class="reference-links" style="margin-top:2em;">
      <h3>Links</h3>
      <ul>
        <?php if ($url): ?>
          <li><a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">View Source</a></li>
        <?php endif; ?>
        <?php if ($archive_url): ?>
          <li><a href="<?php echo esc_url($archive_url); ?>" target="_blank" rel="noopener noreferrer">View Archive</a></li>
        <?php endif; ?>
      </ul>
    </div>
  <?php endif; ?>


  <?php
  // Chapters that reference this Reference CPT
  $chapters = get_posts([
    'post_type'      => 'chapter',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'meta_query'     => [
      [
        'key'     => 'references_cited', // ACF relationship field on chapters
        'value'   => $reference_id,
        'compare' => 'LIKE'
      ]
    ]
  ]);

  if ($chapters): ?>
    <div class="reference-chapters" style="margin-top:3em; text-align:center;">
      <h3>Referenced In</h3>
      <ul style="list-style:none; padding:0; display:inline-block; text-align:left;">
        <?php foreach ($chapters as $chapter): ?>
          <li>
            <a href="<?php echo get_permalink($chapter->ID); ?>">
              <?php echo esc_html(get_the_title($chapter->ID)); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php get_template_part('content/reference-nav'); ?>
</div>
