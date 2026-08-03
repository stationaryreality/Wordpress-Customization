<?php
$current_id = get_the_ID();

$chapters = get_posts(array(
  'post_type'        => 'chapter',
  'posts_per_page'   => -1,
  'orderby'          => 'menu_order',
  'order'            => 'ASC',
  'suppress_filters' => false,
  'fields'           => 'ids',
));

$current_index = array_search($current_id, $chapters);

// Next is forward in order (appears on LEFT)
$next_id = $chapters[$current_index + 1] ?? null;

// Previous is backward in order (appears on RIGHT)
$prev_id = $chapters[$current_index - 1] ?? null;
?>

<div class="cpt-chapter-nav-bottom">
  <?php if ($next_id): ?>
    <div class="cpt-chapter-nav-next">
      <h2>Next Chapter</h2>
      <a href="<?php echo get_permalink($next_id); ?>">
        <?php echo get_the_post_thumbnail($next_id, 'medium'); ?>
        <h3><?php echo get_the_title($next_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>

  <?php if ($prev_id): ?>
    <div class="cpt-chapter-nav-prev">
      <h2>Previous Chapter</h2>
      <a href="<?php echo get_permalink($prev_id); ?>">
        <?php echo get_the_post_thumbnail($prev_id, 'medium'); ?>
        <h3><?php echo get_the_title($prev_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>
</div>