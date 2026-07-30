<?php
$image_id = get_the_ID();
$caption  = get_field('image_caption', $image_id);
$image    = get_field('image_file', $image_id);
// Changed to medium size as requested
$img_url  = $image ? $image['sizes']['medium'] : get_the_post_thumbnail_url($image_id, 'medium');
$img_full = $image ? $image['url'] : '';
?>

<div class="cpt-image-content">

  <!-- Navigation moved to TOP to encourage scrolling -->
  <?php get_template_part('template-parts/navigation/image'); ?>

  <div class="cpt-image-main">
    <?php if ($img_url): ?>
      <a href="<?php echo esc_url($img_full); ?>">
        <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>">
      </a>
    <?php endif; ?>
  </div>

  <h1><?php the_title(); ?></h1>

  <div class="cpt-image-caption">
    <?php if ($caption): ?>
      <?php echo wp_kses_post($caption); ?>
    <?php else: ?>
      <?php the_content(); ?>
    <?php endif; ?>
  </div>

  <div style="text-align:center;">
    <?php echo kp_render_references($image_id); ?>
  </div>

  <?php show_featured_in_threads('images_linked'); ?>

  <div style="text-align:center;">
    <?php echo fn_taxonomy_bubbles($image_id); ?>
  </div>

</div>