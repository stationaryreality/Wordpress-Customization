<?php
$current_id = get_the_ID();
$song_ids = get_posts([
  'post_type' => 'song',
  'numberposts' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
  'fields' => 'ids',
]);

$current_index = array_search($current_id, $song_ids);
$next_id = $song_ids[$current_index + 1] ?? null;
$prev_id = $song_ids[$current_index - 1] ?? null;
?>

<div class="post-navigation-container song-nav" style="display: flex; justify-content: center; gap: 60px; margin-top: 60px;">
  <?php if ($next_id): ?>
    <div class="previous-post" style="text-align: center;">
      <h2>Next Song</h2>
      <a href="<?php echo get_permalink($next_id); ?>" style="display: inline-block;">
        <?php
        $cover = get_field('cover_image', $next_id);
        if ($cover) {
          echo '<img src="' . esc_url($cover['sizes']['thumbnail']) . '" alt="' . esc_attr(get_the_title($next_id)) . '" style="width:100px;height:100px;object-fit:cover;border-radius:0;margin-bottom:10px;display:block;margin:0 auto;">';
        }
        ?>
        <h3><?php echo get_the_title($next_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>

  <?php if ($prev_id): ?>
    <div class="next-post" style="text-align: center;">
      <h2>Previous Song</h2>
      <a href="<?php echo get_permalink($prev_id); ?>" style="display: inline-block;">
        <?php
        $cover = get_field('cover_image', $prev_id);
        if ($cover) {
          echo '<img src="' . esc_url($cover['sizes']['thumbnail']) . '" alt="' . esc_attr(get_the_title($prev_id)) . '" style="width:100px;height:100px;object-fit:cover;border-radius:0;margin-bottom:10px;display:block;margin:0 auto;">';
        }
        ?>
        <h3><?php echo get_the_title($prev_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>
</div>
