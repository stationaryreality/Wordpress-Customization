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

<div class="cpt-concept-nav">
  <?php if ($next_id): ?>
    <div class="cpt-concept-nav-next">
      <h2>Next Concept</h2>
      <a href="<?php echo get_permalink($next_id); ?>">
        <?php echo get_the_post_thumbnail($next_id, 'medium', ['class' => 'cpt-concept-nav-img']); ?>
        <h3><?php echo get_the_title($next_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>

  <?php if ($prev_id): ?>
    <div class="cpt-concept-nav-prev">
      <h2>Previous Concept</h2>
      <a href="<?php echo get_permalink($prev_id); ?>">
        <?php echo get_the_post_thumbnail($prev_id, 'medium', ['class' => 'cpt-concept-nav-img']); ?>
        <h3><?php echo get_the_title($prev_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>
</div>