<?php
/**
 * Template Name: Quotes Directory
 */
get_header();

$quotes = get_posts([
  'post_type' => 'quote',
  'numberposts' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
]);
?>

<main class="quotes-directory">
  <section class="container" style="max-width: 800px; margin: auto; padding: 2rem 1rem;">
    <h1>Quotes</h1>
    <p class="intro-text">Collected quotes from books, chapters, and profiles across the site.</p>

    <div class="quote-list">
      <?php foreach ($quotes as $quote): ?>
        <?php $excerpt = get_field('quote_html_block', $quote->ID); ?>
        <div class="quote-entry" style="margin-bottom: 2rem; border-bottom: 1px solid #ddd; padding-bottom: 1rem;">
          <h2 style="margin-bottom: 0.5rem;">
            <a href="<?php echo get_permalink($quote); ?>">
              <?php echo esc_html(get_the_title($quote)); ?>
            </a>
          </h2>
          <?php if ($excerpt): ?>
            <p style="margin: 0;"><?php echo esc_html(wp_trim_words(strip_tags($excerpt), 30)); ?></p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php get_footer(); ?>
