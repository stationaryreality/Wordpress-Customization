<?php
// Alphabetical navigation for Concept CPT (reversed intentionally)
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

<div class="post-navigation-container concept-nav" style="display: flex; justify-content: space-between; gap: 20px; margin-top: 40px;">
  <?php if ($next_id): ?>
    <div class="previous-post">
      <h2>Next Concept</h2>
<div class="post-thumbnail concept-nav-thumb">
  <a href="<?php echo get_permalink($next_id); ?>">
    <?php echo get_the_post_thumbnail($next_id, 'medium'); ?>
  </a>
</div>
      <h3><a href="<?php echo get_permalink($next_id); ?>"><?php echo get_the_title($next_id); ?></a></h3>
    </div>
  <?php endif; ?>

  <?php if ($prev_id): ?>
    <div class="next-post">
      <h2>Previous Concept</h2>
<div class="post-thumbnail concept-nav-thumb">
  <a href="<?php echo get_permalink($prev_id); ?>">
    <?php echo get_the_post_thumbnail($prev_id, 'medium'); ?>
  </a>
</div>
      <h3><a href="<?php echo get_permalink($prev_id); ?>"><?php echo get_the_title($prev_id); ?></a></h3>
    </div>
  <?php endif; ?>
</div>
