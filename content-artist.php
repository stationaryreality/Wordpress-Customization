<?php
$bio = get_field('bio'); // Optional ACF field
$portrait = get_field('portrait_image', get_the_ID());
$img_url  = $portrait ? $portrait['sizes']['medium'] : '';
?>

<div class="person-content">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="author-thumbnail">
  <?php endif; ?>

  <h1><?php the_title(); ?></h1>

  <div class="person-bio">
    <?php if ($bio): ?>
      <?php echo wp_kses_post($bio); ?>
    <?php else: ?>
      <?php the_content(); ?>
    <?php endif; ?>
  </div>

  <?php get_template_part('content/artist-nav'); ?>
</div>
