/* Template Name: Songs Featured */
<?php
$current_id = get_the_ID();
$artist_tiers = kp_get_artist_tiers();
$priority = ['thread', 'episode', 'strand', 'featured', 'referenced'];

// Find this artist's single tier
$current_tier = 'referenced';
foreach ($priority as $t) {
    if (in_array($current_id, $artist_tiers[$t])) {
        $current_tier = $t;
        break;
    }
}

// Rap / non-rap separation
$is_rapper = has_term('rapper', 'artist_type', $current_id);
$rappers = get_posts([
    'post_type'      => 'artist',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'tax_query'      => [['taxonomy' => 'artist_type', 'field' => 'slug', 'terms' => 'rapper']]
]);

// Keep only artists matching the current artist's rap status
$artist_ids = array_values(array_filter($artist_tiers[$current_tier], function($aid) use ($rappers, $is_rapper) {
    return in_array($aid, $rappers) === $is_rapper;
}));

// Sort alphabetically by title
usort($artist_ids, function($a, $b) {
    return strcmp(get_the_title($a), get_the_title($b));
});

// Prev / Next
$current_index = array_search($current_id, $artist_ids);
$next_id = ($current_index !== false) ? ($artist_ids[$current_index + 1] ?? null) : null;
$prev_id = ($current_index !== false) ? ($artist_ids[$current_index - 1] ?? null) : null;

$labels = [
    'thread'     => 'Narrative Thread',
    'episode'    => 'Narrative Episode',
    'strand'     => 'Narrative Strand',
    'featured'   => 'Featured',
    'referenced' => 'Referenced'
];
$nav_label = $labels[$current_tier] . ' Artist';
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