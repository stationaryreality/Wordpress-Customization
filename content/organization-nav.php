<?php
$current_id = get_the_ID();
$org_ids = get_posts([
  'post_type' => 'organization',
  'numberposts' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
  'fields' => 'ids',
]);

$current_index = array_search($current_id, $org_ids);
$next_id = $org_ids[$current_index + 1] ?? null;
$prev_id = $org_ids[$current_index - 1] ?? null;
?>

<div class="post-navigation-container people-nav" style="display: flex; justify-content: space-between; gap: 20px; margin-top: 40px;">
  <?php if ($next_id): ?>
    <div class="previous-post">
      <h2>Next Organization</h2>
      <a href="<?php echo get_permalink($next_id); ?>">
        <?php
        $cover = get_field('cover_image', $next_id);
        if ($cover) {
          echo '<img src="' . esc_url($cover['sizes']['thumbnail']) . '" alt="' . esc_attr(get_the_title($next_id)) . '" style="width:100px;height:100px;object-fit:cover;border-radius:0;margin-bottom:10px;">';
        }
        ?>
        <h3><?php echo get_the_title($next_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>

  <?php if ($prev_id): ?>
    <div class="next-post">
      <h2>Previous Organization</h2>
      <a href="<?php echo get_permalink($prev_id); ?>">
        <?php
        $cover = get_field('cover_image', $prev_id);
        if ($cover) {
          echo '<img src="' . esc_url($cover['sizes']['thumbnail']) . '" alt="' . esc_attr(get_the_title($prev_id)) . '" style="width:100px;height:100px;object-fit:cover;border-radius:0;margin-bottom:10px;">';
        }
        ?>
        <h3><?php echo get_the_title($prev_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>
</div>
