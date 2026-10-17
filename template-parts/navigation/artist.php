<?php
$current_id   = get_the_ID();
$tiers        = kp_get_tiers($current_id);
$primary_tier = $tiers[0]; // Highest priority tier
$tier_label   = kp_get_tier_label($primary_tier);

$artist_ids = kp_get_tier_posts($primary_tier, 'artist');

$current_index = array_search($current_id, $artist_ids);
if ($current_index !== false) {
    $next_id = $artist_ids[$current_index + 1] ?? null;
    $prev_id = $artist_ids[$current_index - 1] ?? null;
} else {
    $next_id = null;
    $prev_id = null;
}

$nav_label = $tier_label . ' Artist';
?>

<div class="cpt-artist-nav-top">
  <div class="cpt-artist-nav-row">
    <?php if ($prev_id): ?>
      <?php $portrait = get_field('portrait_image', $prev_id); $thumb_url = $portrait ? $portrait['sizes']['thumbnail'] : ''; ?>
      <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-artist-nav-prev cpt-keyboard-nav-prev">
        <span class="cpt-artist-nav-label">← Previous <?php echo esc_html($nav_label); ?></span>
        <?php if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($prev_id)); ?>" class="cpt-artist-nav-thumb">
        <?php endif; ?>
        <span class="cpt-artist-nav-title"><?php echo get_the_title($prev_id); ?></span>
      </a>
    <?php endif; ?>

    <?php if ($prev_id || $next_id): ?>
      <span class="cpt-keyboard-hint-inline" title="Use arrow keys to navigate">Use ← ⌨️ → keys</span>
    <?php endif; ?>

    <?php if ($next_id): ?>
      <?php $portrait = get_field('portrait_image', $next_id); $thumb_url = $portrait ? $portrait['sizes']['thumbnail'] : ''; ?>
      <a href="<?php echo get_permalink($next_id); ?>" class="cpt-artist-nav-next cpt-keyboard-nav-next">
        <span class="cpt-artist-nav-label">Next <?php echo esc_html($nav_label); ?> →</span>
        <?php if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($next_id)); ?>" class="cpt-artist-nav-thumb">
        <?php endif; ?>
        <span class="cpt-artist-nav-title"><?php echo get_the_title($next_id); ?></span>
      </a>
    <?php endif; ?>
  </div>
</div>