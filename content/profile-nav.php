<?php
// Alphabetical navigation for Profile CPT
$current_id = get_the_ID();
$people_ids = get_posts([
  'post_type' => 'profile',
  'numberposts' => -1,
  'posts_per_page' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
  'fields' => 'ids',
]);

$current_index = array_search($current_id, $people_ids);
$next_id = $people_ids[$current_index + 1] ?? null; // NEXT in alphabet
$prev_id = $people_ids[$current_index - 1] ?? null; // PREVIOUS in alphabet
?>

<div class="post-navigation-container people-nav" style="display: flex; justify-content: space-between; gap: 20px; margin-top: 40px;">
  <?php if ($next_id): ?>
    <div class="previous-post" style="text-align: center;">
      <h2>Next Person</h2>
      <a href="<?php echo get_permalink($next_id); ?>">
        <?php
        $portrait = get_field('portrait_image', $next_id);
        if ($portrait) {
          echo '<img src="' . esc_url($portrait['sizes']['thumbnail']) . '" alt="' . esc_attr(get_the_title($next_id)) . '" style="width:100px;height:100px;object-fit:cover;border-radius:50%;margin-bottom:10px;">';
        }
        ?>
        <h3><?php echo get_the_title($next_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>

  <?php if ($prev_id): ?>
    <div class="next-post" style="text-align: center;">
      <h2>Previous Person</h2>
      <a href="<?php echo get_permalink($prev_id); ?>">
        <?php
        $portrait = get_field('portrait_image', $prev_id);
        if ($portrait) {
          echo '<img src="' . esc_url($portrait['sizes']['thumbnail']) . '" alt="' . esc_attr(get_the_title($prev_id)) . '" style="width:100px;height:100px;object-fit:cover;border-radius:50%;margin-bottom:10px;">';
        }
        ?>
        <h3><?php echo get_the_title($prev_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>
</div>
