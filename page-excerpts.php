<?php
/**
 * Template Name: Excerpts Directory
 */
get_header();

$excerpts = get_posts([
  'post_type'   => 'excerpt',
  'numberposts' => -1,
  'orderby'     => 'title',
  'order'       => 'ASC',
]);
?>

<main class="excerpts-directory">
  <section class="container" style="max-width: 800px; margin: auto; padding: 2rem 1rem;">
    <h1>Excerpts</h1>
    <p class="intro-text">Collected excerpts from books, articles, and other referenced sources across the site.</p>

    <div class="excerpt-list">
      <?php foreach ($excerpts as $excerpt): ?>
        <?php 
          // Source: could be a Book, Article, Podcast, Profile, etc.
          $source = get_field('excerpt_source', $excerpt->ID); 
          $source_link = $source ? get_permalink($source->ID) : '';
          $source_title = $source ? get_the_title($source->ID) : '';
        ?>
        <div class="excerpt-entry" style="margin-bottom: 2rem; border-bottom: 1px solid #ddd; padding-bottom: 1rem;">
          <h2 style="margin-bottom: 0.5rem;">
            <a href="<?php echo get_permalink($excerpt); ?>">
              <?php echo esc_html(get_the_title($excerpt)); ?>
            </a>
          </h2>

          <?php if ($source): ?>
            <p style="margin: 0; font-size: 0.9rem; color: #666;">
              Source: <a href="<?php echo esc_url($source_link); ?>"><?php echo esc_html($source_title); ?></a>
            </p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php get_footer(); ?>
