<?php
$current_id = get_the_ID();
$excerpt_ids = get_posts([
  'post_type' => 'excerpt',
  'posts_per_page' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
  'fields' => 'ids',
]);

$current_index = array_search($current_id, $excerpt_ids);
$next_id = $excerpt_ids[$current_index + 1] ?? null;
$prev_id = $excerpt_ids[$current_index - 1] ?? null;
?>

<div class="cpt-excerpt-nav-top">
  <div class="cpt-excerpt-nav-row">
    <?php if ($prev_id): ?>
      <?php $thumb_url = get_the_post_thumbnail_url($prev_id, 'thumbnail'); ?>
      <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-excerpt-nav-prev cpt-keyboard-nav-prev">
        <span class="cpt-excerpt-nav-label">← Previous Excerpt</span>
        <?php if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($prev_id)); ?>" class="cpt-excerpt-nav-thumb">
        <?php endif; ?>
        <span class="cpt-excerpt-nav-title"><?php echo get_the_title($prev_id); ?></span>
      </a>
    <?php endif; ?>

    <?php if ($prev_id || $next_id): ?>
      <span class="cpt-keyboard-hint-inline" title="Use arrow keys to navigate">
        Use ← ⌨️ → keys
      </span>
    <?php endif; ?>

    <?php if ($next_id): ?>
      <?php $thumb_url = get_the_post_thumbnail_url($next_id, 'thumbnail'); ?>
      <a href="<?php echo get_permalink($next_id); ?>" class="cpt-excerpt-nav-next cpt-keyboard-nav-next">
        <span class="cpt-excerpt-nav-label">Next Excerpt →</span>
        <?php if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($next_id)); ?>" class="cpt-excerpt-nav-thumb">
        <?php endif; ?>
        <span class="cpt-excerpt-nav-title"><?php echo get_the_title($next_id); ?></span>
      </a>
    <?php endif; ?>
  </div>
</div>