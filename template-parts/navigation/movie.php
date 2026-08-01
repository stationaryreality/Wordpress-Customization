<?php
$current_id = get_the_ID();
$movie_ids = get_posts([
  'post_type' => 'movie',
  'posts_per_page' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
  'fields' => 'ids',
]);

$current_index = array_search($current_id, $movie_ids);
$next_id = $movie_ids[$current_index + 1] ?? null;
$prev_id = $movie_ids[$current_index - 1] ?? null;
?>

<div class="cpt-movie-nav-top">
  <div class="cpt-movie-nav-row">
    <?php if ($prev_id): ?>
      <?php
      $cover = get_field('cover_image', $prev_id);
      $thumb_url = $cover ? $cover['sizes']['thumbnail'] : '';
      ?>
      <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-movie-nav-prev cpt-keyboard-nav-prev">
        <span class="cpt-movie-nav-label">← Previous Movie</span>
        <?php if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($prev_id)); ?>" class="cpt-movie-nav-thumb">
        <?php endif; ?>
        <span class="cpt-movie-nav-title"><?php echo get_the_title($prev_id); ?></span>
      </a>
    <?php endif; ?>

    <?php if ($prev_id || $next_id): ?>
      <span class="cpt-keyboard-hint-inline" title="Use arrow keys to navigate">
        Use ← ⌨️ → keys
      </span>
    <?php endif; ?>

    <?php if ($next_id): ?>
      <?php
      $cover = get_field('cover_image', $next_id);
      $thumb_url = $cover ? $cover['sizes']['thumbnail'] : '';
      ?>
      <a href="<?php echo get_permalink($next_id); ?>" class="cpt-movie-nav-next cpt-keyboard-nav-next">
        <span class="cpt-movie-nav-label">Next Movie →</span>
        <?php if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($next_id)); ?>" class="cpt-movie-nav-thumb">
        <?php endif; ?>
        <span class="cpt-movie-nav-title"><?php echo get_the_title($next_id); ?></span>
      </a>
    <?php endif; ?>
  </div>
</div>