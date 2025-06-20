<?php
$definition = get_field('definition'); // Custom ACF field for concepts
$portrait   = get_field('portrait_image', get_the_ID());
$img_url    = $portrait ? $portrait['sizes']['thumbnail'] : '';
?>

<div class="person-content">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="author-thumbnail">
  <?php endif; ?>

  <h1><?php the_title(); ?></h1>

  <div class="person-bio">
    <?php if ($definition): ?>
      <?php echo wp_kses_post($definition); ?>
    <?php else: ?>
      <?php the_content(); ?>
    <?php endif; ?>
  </div>

  <?php get_template_part('content/concept-nav'); ?>
</div>
