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
  <?php if ($prev_id): ?>
    <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-concept-nav-prev cpt-keyboard-nav-prev">
      <?php echo get_the_post_thumbnail($prev_id, 'medium', ['class' => 'cpt-concept-nav-img']); ?>
      <span class="cpt-concept-nav-label">← Previous</span>
    </a>
  <?php endif; ?>

  <?php if ($next_id): ?>
    <a href="<?php echo get_permalink($next_id); ?>" class="cpt-concept-nav-next cpt-keyboard-nav-next">
      <?php echo get_the_post_thumbnail($next_id, 'medium', ['class' => 'cpt-concept-nav-img']); ?>
      <span class="cpt-concept-nav-label">Next →</span>
    </a>
  <?php endif; ?>
  
  <?php if ($prev_id || $next_id): ?>
    <span class="cpt-keyboard-hint" title="Use ← → arrow keys to navigate">
      ⌨️
    </span>
  <?php endif; ?>
</div>