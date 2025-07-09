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
        <?php 
          $excerpt = get_field('quote_plain_text', $quote->ID);
          $profile = get_field('profile_cited', $quote->ID); // should return a post object
          $profile_link = $profile ? get_permalink($profile->ID) : '';
          $profile_name = $profile ? get_the_title($profile->ID) : '';
          $portrait = $profile ? get_field('portrait_image', $profile->ID) : '';
        ?>
        <div class="quote-entry" style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 2rem; border-bottom: 1px solid #ddd; padding-bottom: 1rem;">
          <?php if ($portrait): ?>
            <a href="<?php echo esc_url($profile_link); ?>" class="quote-portrait">
              <img src="<?php echo esc_url($portrait['sizes']['thumbnail']); ?>" alt="<?php echo esc_attr($profile_name); ?>" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
            </a>
          <?php endif; ?>

          <div class="quote-text">
            <h2 style="margin-bottom: 0.5rem;">
              <a href="<?php echo get_permalink($quote); ?>">
                <?php echo esc_html(get_the_title($quote)); ?>
              </a>
            </h2>
            <?php if ($excerpt): ?>
              <p style="margin: 0;"><?php echo esc_html(wp_trim_words($excerpt, 30)); ?></p>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php get_footer(); ?>
