<?php
$current_id = get_the_ID();

$element_ids = get_posts([
  'post_type'   => 'element',
  'numberposts' => -1,
  'orderby'     => 'title',
  'order'       => 'ASC',
  'fields'      => 'ids',
]);


$current_index = array_search($current_id, $element_ids);
$next_id = $element_ids[$current_index + 1] ?? null;
$prev_id = $element_ids[$current_index - 1] ?? null;
?>

<div class="post-navigation-container element-nav" style="display:flex;justify-content:center;gap:60px;margin-top:60px;">
  <?php if ($next_id): ?>
    <div class="previous-post" style="text-align:center;">
      <h2>Next Element</h2>
      <a href="<?php echo get_permalink($next_id); ?>" style="display:inline-block;">
        <?php
        // Use ACF image if available, fallback to featured
        $cover = get_field('image_file', $next_id);
        if ($cover && isset($cover['sizes']['thumbnail'])) {
          $thumb_url = $cover['sizes']['thumbnail'];
        } else {
          $thumb_url = get_the_post_thumbnail_url($next_id, 'thumbnail');
        }

        if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>"
               alt="<?php echo esc_attr(get_the_title($next_id)); ?>"
               style="width:100px;height:100px;object-fit:cover;border-radius:0;margin-bottom:10px;display:block;margin:0 auto;">
        <?php endif; ?>
        <h3><?php echo get_the_title($next_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>

  <?php if ($prev_id): ?>
    <div class="next-post" style="text-align:center;">
      <h2>Previous Element</h2>
      <a href="<?php echo get_permalink($prev_id); ?>" style="display:inline-block;">
        <?php
        $cover = get_field('image_file', $prev_id);
        if ($cover && isset($cover['sizes']['thumbnail'])) {
          $thumb_url = $cover['sizes']['thumbnail'];
        } else {
          $thumb_url = get_the_post_thumbnail_url($prev_id, 'thumbnail');
        }

        if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>"
               alt="<?php echo esc_attr(get_the_title($prev_id)); ?>"
               style="width:100px;height:100px;object-fit:cover;border-radius:0;margin-bottom:10px;display:block;margin:0 auto;">
        <?php endif; ?>
        <h3><?php echo get_the_title($prev_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>
</div>
