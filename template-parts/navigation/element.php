<?php
$current_id = get_the_ID();
$element_ids = get_posts([
  'post_type' => 'element',
  'numberposts' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
  'fields' => 'ids',
]);

$current_index = array_search($current_id, $element_ids);
$next_id = $element_ids[$current_index + 1] ?? null;
$prev_id = $element_ids[$current_index - 1] ?? null;
?>

<div class="cpt-element-nav">
  <?php if ($next_id): ?>
    <div class="cpt-element-nav-next">
      <h2>Next Element</h2>
      <a href="<?php echo get_permalink($next_id); ?>">
        <?php
        $cover = get_field('image_file', $next_id);
        $thumb_url = ($cover && isset($cover['sizes']['thumbnail'])) ? $cover['sizes']['thumbnail'] : get_the_post_thumbnail_url($next_id, 'thumbnail');
        if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($next_id)); ?>" class="cpt-element-nav-img">
        <?php endif; ?>
        <h3><?php echo get_the_title($next_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>

  <?php if ($prev_id): ?>
    <div class="cpt-element-nav-prev">
      <h2>Previous Element</h2>
      <a href="<?php echo get_permalink($prev_id); ?>">
        <?php
        $cover = get_field('image_file', $prev_id);
        $thumb_url = ($cover && isset($cover['sizes']['thumbnail'])) ? $cover['sizes']['thumbnail'] : get_the_post_thumbnail_url($prev_id, 'thumbnail');
        if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($prev_id)); ?>" class="cpt-element-nav-img">
        <?php endif; ?>
        <h3><?php echo get_the_title($prev_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>
</div>