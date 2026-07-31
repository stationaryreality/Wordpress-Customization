<?php
$current_id = get_the_ID();

// Get fragments in the custom Post Types Order
$fragment_ids = get_posts([
  'post_type'        => 'fragment',
  'posts_per_page'   => -1,
  'orderby'          => 'menu_order',
  'order'            => 'ASC',
  'suppress_filters' => false, // Allows Post Types Order plugin to work
  'fields'           => 'ids',
]);

$current_index = array_search($current_id, $fragment_ids);

// "Next" is forward in the order (appears on the LEFT)
$next_id = $fragment_ids[$current_index + 1] ?? null;

// "Previous" is backward in the order (appears on the RIGHT)
$prev_id = $fragment_ids[$current_index - 1] ?? null;
?>

<div class="cpt-fragment-nav-bottom">
  <?php if ($next_id): ?>
    <div class="cpt-fragment-nav-next">
      <h2>Next Fragment</h2>
      <a href="<?php echo get_permalink($next_id); ?>">
        <?php echo get_the_post_thumbnail($next_id, 'medium'); ?>
        <h3><?php echo get_the_title($next_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>

  <?php if ($prev_id): ?>
    <div class="cpt-fragment-nav-prev">
      <h2>Previous Fragment</h2>
      <a href="<?php echo get_permalink($prev_id); ?>">
        <?php echo get_the_post_thumbnail($prev_id, 'medium'); ?>
        <h3><?php echo get_the_title($prev_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>
</div>