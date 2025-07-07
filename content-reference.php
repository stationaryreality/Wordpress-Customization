<?php
$description = get_field('description');
$image       = get_field('cover_image');
$img_url     = $image ? $image['sizes']['thumbnail'] : '';

$source_name = get_field('source_name');
$credit_name = get_field('credit_name');
$url         = get_field('url');
$archive_url = get_field('archive_link');
$notes       = get_field('citation_notes');
$related     = get_field('related_cpt');
?>

<div class="reference-content">
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

  <?php if ($related): ?>
    <p class="reference-related">
      <strong>Original Chapter:</strong>
      <a href="<?php echo get_permalink($related); ?>">
        <?php echo esc_html(get_the_title($related)); ?>
      </a>
    </p>
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

  <?php if ($notes): ?>
    <div class="reference-notes">
      <h3>Details</h3>
      <ul>
        <?php foreach ($notes as $note): ?>
          <li><strong><?php echo esc_html($note['label']); ?>:</strong> <?php echo esc_html($note['value']); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($url || $archive_url): ?>
    <div class="reference-links">
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

  <?php get_template_part('content/reference-nav'); ?>
</div>
