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

<div class="cpt-element-nav-top">
  <div class="cpt-element-nav-row">
    <?php if ($prev_id): ?>
      <?php
      $cover = get_field('image_file', $prev_id);
      $thumb_url = ($cover && isset($cover['sizes']['thumbnail'])) ? $cover['sizes']['thumbnail'] : get_the_post_thumbnail_url($prev_id, 'thumbnail');
      ?>
      <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-element-nav-prev cpt-keyboard-nav-prev">
        <span class="cpt-element-nav-label">← Previous Element</span>
        <?php if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($prev_id)); ?>" class="cpt-element-nav-thumb">
        <?php endif; ?>
        <span class="cpt-element-nav-title"><?php echo get_the_title($prev_id); ?></span>
      </a>
    <?php endif; ?>

    <?php if ($prev_id || $next_id): ?>
      <span class="cpt-keyboard-hint-inline" title="Use arrow keys to navigate">
        Use ← ⌨️ → keys
      </span>
    <?php endif; ?>

    <?php if ($next_id): ?>
      <?php
      $cover = get_field('image_file', $next_id);
      $thumb_url = ($cover && isset($cover['sizes']['thumbnail'])) ? $cover['sizes']['thumbnail'] : get_the_post_thumbnail_url($next_id, 'thumbnail');
      ?>
      <a href="<?php echo get_permalink($next_id); ?>" class="cpt-element-nav-next cpt-keyboard-nav-next">
        <span class="cpt-element-nav-label">Next Element →</span>
        <?php if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($next_id)); ?>" class="cpt-element-nav-thumb">
        <?php endif; ?>
        <span class="cpt-element-nav-title"><?php echo get_the_title($next_id); ?></span>
      </a>
    <?php endif; ?>
  </div>
</div>