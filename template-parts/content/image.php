<?php
/**
 * Template Part: Single Image Page Content
 *
 * Displays a single image with constrained size and lightbox.
 * Keeps all other page elements (caption, references, threads, bubbles, nav).
 */
$image_id = get_the_ID();
$caption  = get_field('image_caption', $image_id);
$image    = get_field('image_file', $image_id);
$img_large_url  = $image ? $image['sizes']['large'] : '';
$img_full_url   = $image ? $image['url'] : '';
$img_alt        = get_the_title();
?>

<div class="single-image-wrapper">

  <!-- Constrained image with lightbox link -->
  <div class="single-image-container">
    <?php if ($img_large_url): ?>
      <a href="<?php echo esc_url($img_full_url); ?>" class="single-image-lightbox-link">
        <img 
          src="<?php echo esc_url($img_large_url); ?>" 
          alt="<?php echo esc_attr($img_alt); ?>"
          class="single-image-display"
        >
      </a>
    <?php endif; ?>
  </div>

  <!-- Title -->
  <h1 class="single-image-title"><?php the_title(); ?></h1>

  <!-- Caption or content -->
  <div class="single-image-caption">
    <?php if ($caption): ?>
      <?php echo wp_kses_post($caption); ?>
    <?php else: ?>
      <?php the_content(); ?>
    <?php endif; ?>
  </div>

  <!-- References -->
  <div class="single-image-references">
    <?php echo kp_render_references(get_the_ID()); ?>
  </div>

  <!-- Featured in threads -->
  <?php show_featured_in_threads('images_linked'); ?>

  <!-- Taxonomy bubbles -->
  <div class="single-image-taxonomies">
    <?php echo fn_taxonomy_bubbles(get_the_ID()); ?>
  </div>

  <!-- Navigation (prev/next) -->
  <?php get_template_part('template-parts/navigation/image'); ?>

</div>

<!-- ===== SIMPLE LIGHTBOX (built-in) ===== -->
<div class="single-image-lightbox-overlay" id="lightboxOverlay">
  <img id="lightboxImage" src="" alt="Full size image">
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('lightboxOverlay');
    const lightboxImg = document.getElementById('lightboxImage');

    // Open lightbox on any link with class 'single-image-lightbox-link'
    document.querySelectorAll('.single-image-lightbox-link').forEach(function(link) {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        lightboxImg.src = link.href;
        overlay.classList.add('active');
      });
    });

    // Close lightbox on overlay click
    overlay.addEventListener('click', function() {
      overlay.classList.remove('active');
      lightboxImg.src = '';
    });

    // Close lightbox with Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && overlay.classList.contains('active')) {
        overlay.classList.remove('active');
        lightboxImg.src = '';
      }
    });
  });
</script>