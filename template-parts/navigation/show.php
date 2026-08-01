<?php
$current_id = get_the_ID();
$show_ids = get_posts([
  'post_type' => 'show',
  'posts_per_page' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
  'fields' => 'ids',
]);

$current_index = array_search($current_id, $show_ids);
$next_id = $show_ids[$current_index + 1] ?? null;
$prev_id = $show_ids[$current_index - 1] ?? null;
?>

<div class="cpt-show-nav-top">
  <div class="cpt-show-nav-row">
    <?php if ($prev_id): ?>
      <?php
      $cover = get_field('cover_image', $prev_id);
      $thumb_url = $cover ? $cover['sizes']['thumbnail'] : '';
      ?>
      <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-show-nav-prev cpt-keyboard-nav-prev">
        <span class="cpt-show-nav-label">← Previous Show</span>
        <?php if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($prev_id)); ?>" class="cpt-show-nav-thumb">
        <?php endif; ?>
        <span class="cpt-show-nav-title"><?php echo get_the_title($prev_id); ?></span>
      </a>
    <?php endif; ?>

    <?php if ($prev_id || $next_id): ?>
      <span class="cpt-keyboard-hint-inline" title="Use arrow keys to navigate">
        Use ← ⌨️ → keys
      </span>
    <?php endif; ?>

    <?php if ($next_id): ?>
      <?php
      $cover = get_field('cover_image', $next_id);
      $thumb_url = $cover ? $cover['sizes']['thumbnail'] : '';
      ?>
      <a href="<?php echo get_permalink($next_id); ?>" class="cpt-show-nav-next cpt-keyboard-nav-next">
        <span class="cpt-show-nav-label">Next Show →</span>
        <?php if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($next_id)); ?>" class="cpt-show-nav-thumb">
        <?php endif; ?>
        <span class="cpt-show-nav-title"><?php echo get_the_title($next_id); ?></span>
      </a>
    <?php endif; ?>
  </div>
</div>