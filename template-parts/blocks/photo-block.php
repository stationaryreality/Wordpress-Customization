<?php
$image = get_field('photo_image');
$caption = get_field('photo_caption');
$source = get_field('photo_source');
?>

<div class="chapter-block">
  <figure class="wp-block-image size-large">
    <?php if ($image): ?>
      <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
    <?php endif; ?>

    <?php if ($caption || $source): ?>
      <figcaption>
        <?php if ($caption): ?>
          <?php echo esc_html($caption); ?>
        <?php endif; ?>
        <?php if ($source): ?>
          <span class="source"> — <?php echo esc_html($source); ?></span>
        <?php endif; ?>
      </figcaption>
    <?php endif; ?>
  </figure>
</div>
