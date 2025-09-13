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
          $text   = get_field('excerpt_plain_text', $excerpt->ID);
          $source = get_field('excerpt_source', $excerpt->ID); 
          $source_link  = $source ? get_permalink($source->ID) : '';
          $source_title = $source ? get_the_title($source->ID) : '';

          // Handle source image
          $image = '';
          if ($source) {
            $cover = get_field('cover_image', $source->ID);
            if ($cover) {
              $image = $cover['sizes']['thumbnail'];
            } elseif (has_post_thumbnail($source->ID)) {
              $image = get_the_post_thumbnail_url($source->ID, 'thumbnail');
            }
          }
        ?>
        <div class="excerpt-entry" style="display:flex; align-items:flex-start; gap:1rem; margin-bottom:2rem; border-bottom:1px solid #ddd; padding-bottom:1rem;">
          <?php if ($image): ?>
            <a href="<?php echo esc_url($source_link); ?>" class="excerpt-thumb">
              <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($source_title); ?>" style="width:48px; height:48px; border-radius:50%; object-fit:cover;">
            </a>
          <?php endif; ?>

          <div class="excerpt-text">
            <h2 style="margin-bottom:0.5rem;">
              <a href="<?php echo get_permalink($excerpt); ?>">
                <?php echo esc_html(get_the_title($excerpt)); ?>
              </a>
            </h2>

            <?php if ($text): ?>
              <p style="margin:0;"><?php echo esc_html(wp_trim_words($text, 30, '...')); ?></p>
            <?php endif; ?>

            <?php if ($source): ?>
<p style="margin-top:0.5rem; font-size:0.9rem; color:#666;">
                Source: <a href="<?php echo esc_url($source_link); ?>"><?php echo esc_html($source_title); ?></a>
              </p>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php get_footer(); ?>
