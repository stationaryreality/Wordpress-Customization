<?php
/**
 * Template Name: Reference Directory
 */

get_header();

$references = get_posts([
  'post_type' => 'reference',
  'numberposts' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
]);
?>

<main class="reference-directory">
  <section class="container" style="max-width: 800px; margin: auto; padding: 2rem 1rem;">
    <h1>Research Sources</h1>
    <p class="intro-text">External sources, credits, and citations referenced throughout the site.</p>

    <div class="reference-list">
      <?php foreach ($references as $reference): ?>
        <?php
          $source = get_field('source_name', $reference->ID);
          $description = get_field('description', $reference->ID);
          $url = get_field('url', $reference->ID);
          $credit = get_field('credit_name', $reference->ID);
          $archive = get_field('archive_link', $reference->ID);
        ?>
        <div class="reference-entry" style="margin-bottom: 2rem; border-bottom: 1px solid #ddd; padding-bottom: 1rem;">
          <h2 style="margin-bottom: 0.5rem;">
            <a href="<?php echo get_permalink($reference); ?>">
              <?php echo esc_html(get_the_title($reference)); ?>
            </a>
          </h2>

          <?php if ($source): ?>
            <p style="margin: 0.25rem 0; font-style: italic; color: #666;">
              <?php echo esc_html($source); ?>
            </p>
          <?php endif; ?>

          <?php if ($credit): ?>
            <p style="margin: 0.25rem 0; color: #333;">
              <strong>Credit:</strong> <?php echo esc_html($credit); ?>
            </p>
          <?php endif; ?>

          <?php if ($description): ?>
            <p style="margin: 0.5rem 0;"><?php echo esc_html(wp_trim_words($description, 30)); ?></p>
          <?php endif; ?>

          <?php if ($url || $archive): ?>
            <p style="margin-top: 0.5rem;">
              <?php if ($url): ?>
                <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">View Source</a>
              <?php endif; ?>
              <?php if ($url && $archive): ?> | <?php endif; ?>
              <?php if ($archive): ?>
                <a href="<?php echo esc_url($archive); ?>" target="_blank" rel="noopener noreferrer">View Archive</a>
              <?php endif; ?>
            </p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php get_footer(); ?>
