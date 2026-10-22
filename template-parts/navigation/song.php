<?php
$current_id = get_the_ID();
$tiers = kp_get_song_tiers();
$priority = ['thread','episode','strand','featured','referenced'];

// Find this song's single tier
$current_tier = 'referenced';
foreach ($priority as $t) {
    if (in_array($current_id, $tiers[$t])) {
        $current_tier = $t;
        break;
    }
}

// Rap / non-rap separation
$is_rap = has_term('rap', 'song_category', $current_id);
$rap_songs = get_posts([
    'post_type'      => 'song',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'tax_query'      => [['taxonomy'=>'song_category','field'=>'slug','terms'=>'rap']]
]);

// Keep only songs matching the current song's rap status
$song_ids = array_values(array_filter($tiers[$current_tier], function($sid) use ($rap_songs, $is_rap) {
    return in_array($sid, $rap_songs) === $is_rap;
}));

usort($song_ids, function($a, $b) {
    return strcmp(get_the_title($a), get_the_title($b));
});

$current_index = array_search($current_id, $song_ids);
$next_id = ($current_index !== false) ? ($song_ids[$current_index + 1] ?? null) : null;
$prev_id = ($current_index !== false) ? ($song_ids[$current_index - 1] ?? null) : null;

$labels = ['thread'=>'Narrative Thread','episode'=>'Narrative Episode','strand'=>'Narrative Strand','featured'=>'Featured','referenced'=>'Referenced'];
$heading_prefix = $labels[$current_tier] . ' Song';
?>

<div class="cpt-song-nav-top">
    <div class="cpt-song-nav-row">
        <?php if ($prev_id): ?>
            <?php $cover = get_field('cover_image', $prev_id); $thumb = $cover ? $cover['sizes']['thumbnail'] : ''; ?>
            <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-song-nav-prev cpt-keyboard-nav-prev">
                <span class="cpt-song-nav-label">← Previous <?php echo esc_html($heading_prefix); ?></span>
                <?php if ($thumb): ?>
                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($prev_id)); ?>" class="cpt-song-nav-thumb">
                <?php endif; ?>
                <span class="cpt-song-nav-title"><?php echo get_the_title($prev_id); ?></span>
            </a>
        <?php endif; ?>

        <?php if ($prev_id || $next_id): ?>
            <span class="cpt-keyboard-hint-inline" title="Use arrow keys to navigate">Use ← ⌨️ → keys</span>
        <?php endif; ?>

        <?php if ($next_id): ?>
            <?php $cover = get_field('cover_image', $next_id); $thumb = $cover ? $cover['sizes']['thumbnail'] : ''; ?>
            <a href="<?php echo get_permalink($next_id); ?>" class="cpt-song-nav-next cpt-keyboard-nav-next">
                <span class="cpt-song-nav-label">Next <?php echo esc_html($heading_prefix); ?> →</span>
                <?php if ($thumb): ?>
                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($next_id)); ?>" class="cpt-song-nav-thumb">
                <?php endif; ?>
                <span class="cpt-song-nav-title"><?php echo get_the_title($next_id); ?></span>
            </a>
        <?php endif; ?>
    </div>
</div>