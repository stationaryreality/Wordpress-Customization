<?php
$current_id = get_the_ID();
$image_ids = get_posts([
  'post_type' => 'image',
  'numberposts' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
  'fields' => 'ids',
]);

$current_index = array_search($current_id, $image_ids);
$next_id = $image_ids[$current_index + 1] ?? null;
$prev_id = $image_ids[$current_index - 1] ?? null;
?>

<div class="cpt-image-nav-top">
  <?php if ($prev_id): ?>
    <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-image-nav-prev">
      Previous Image
   a>
  <?php endif; ?>

  <?php if ($next_id): ?>
    <a href="<?php echo get_permalink($next_id); ?>" class="cpt-image-nav-next">
      Next Image
    </a>
  <?php endif; ?>
</div>