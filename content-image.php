<?php
$image_id = get_the_ID();
$caption  = get_field('image_caption', $image_id);
$image    = get_field('image_file', $image_id);
$img_url  = $image ? $image['sizes']['large'] : '';
?>

<div class="image-header" style="text-align:center;">
  <?php if ($img_url): ?>
<img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" style="max-width:600px; width:100%; height:auto; display:block; margin:0 auto 1em;">
  <?php endif; ?>
  <h1><?php the_title(); ?></h1>
</div>

<div class="image-caption">
  <?php if ($caption): ?>
    <?php echo wp_kses_post($caption); ?>
  <?php else: ?>
    <?php the_content(); ?>
  <?php endif; ?>
</div>

<?php get_template_part('content/image-nav'); ?>
