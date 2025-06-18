<?php
$current_id = get_the_ID();
$artist_ids = get_posts([
  'post_type' => 'artist',
  'numberposts' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
  'fields' => 'ids',
]);

$current_index = array_search($current_id, $artist_ids);
$next_id = $artist_ids[$current_index + 1] ?? null;
$prev_id = $artist_ids[$current_index - 1] ?? null;
?>

<div class="post-navigation-container people-nav" style="display: flex; justify-content: space-between; gap: 20px; margin-top: 40px;">
  <?php if ($next_id): ?>
    <div class="previous-post">
      <h2>Next Artist</h2>
      <a href="<?php echo get_permalink($next_id); ?>">
        <?php
        $portrait = get_field('portrait_image', $next_id);
        if ($portrait) {
          echo '<img src="' . esc_url($portrait['sizes']['medium']) . '" alt="' . esc_attr(get_the_title($next_id)) . '" style="width:150px;height:auto;">';
        }
        ?>
        <h3><?php echo get_the_title($next_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>

  <?php if ($prev_id): ?>
    <div class="next-post">
      <h2>Previous Artist</h2>
      <a href="<?php echo get_permalink($prev_id); ?>">
        <?php
        $portrait = get_field('portrait_image', $prev_id);
        if ($portrait) {
          echo '<img src="' . esc_url($portrait['sizes']['medium']) . '" alt="' . esc_attr(get_the_title($prev_id)) . '" style="width:150px;height:auto;">';
        }
        ?>
        <h3><?php echo get_the_title($prev_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>
</div>
