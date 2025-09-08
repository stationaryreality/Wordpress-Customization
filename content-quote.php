<?php
$attribution = get_field('quote_attribution');
$portrait = get_field('portrait_image');
$img_url = $portrait ? $portrait['sizes']['thumbnail'] : '';
?>

<div class="person-content">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="author-thumbnail">
  <?php endif; ?>

  <h1><?php the_title(); ?></h1>

<div class="person-bio">
    <?php the_content(); ?>
    
    <?php if ($attribution): ?>
      <p class="quote-attribution"><?php echo esc_html($attribution); ?></p>
    <?php endif; ?>

  </div>

  <?php get_template_part('content/quote-nav'); ?>
</div>
