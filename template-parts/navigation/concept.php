<?php
$current_id = get_the_ID();
$concept_ids = get_posts([
  'post_type' => 'concept',
  'posts_per_page' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
  'fields' => 'ids',
]);

$current_index = array_search($current_id, $concept_ids);
$next_id = $concept_ids[$current_index + 1] ?? null;
$prev_id = $concept_ids[$current_index - 1] ?? null;
?>

<div class="cpt-concept-nav-top">
  <div class="cpt-concept-nav-row">
    <?php if ($prev_id): ?>
      <?php
      $thumb_url = get_the_post_thumbnail_url($prev_id, 'thumbnail');
      ?>
      <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-concept-nav-prev cpt-keyboard-nav-prev">
        <span class="cpt-concept-nav-label">← Previous Concept</span>
        <?php if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($prev_id)); ?>" class="cpt-concept-nav-thumb">
        <?php endif; ?>
        <span class="cpt-concept-nav-title"><?php echo get_the_title($prev_id); ?></span>
      </a>
    <?php endif; ?>

    <?php if ($prev_id || $next_id): ?>
      <span class="cpt-keyboard-hint-inline" title="Use arrow keys to navigate">
        Use ← ⌨️ → keys
      </span>
    <?php endif; ?>

    <?php if ($next_id): ?>
      <?php
      $thumb_url = get_the_post_thumbnail_url($next_id, 'thumbnail');
      ?>
      <a href="<?php echo get_permalink($next_id); ?>" class="cpt-concept-nav-next cpt-keyboard-nav-next">
        <span class="cpt-concept-nav-label">Next Concept →</span>
        <?php if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($next_id)); ?>" class="cpt-concept-nav-thumb">
        <?php endif; ?>
        <span class="cpt-concept-nav-title"><?php echo get_the_title($next_id); ?></span>
      </a>
    <?php endif; ?>
  </div>
</div>