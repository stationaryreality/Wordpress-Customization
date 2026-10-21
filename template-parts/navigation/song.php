<?php
global $wpdb;
$current_id = get_the_ID();

// ── DETERMINE CURRENT TIER (by priority) ──
$current_tier = 'referenced';

// 1. Check Thread: Is this song referenced by any Chapter?
$thread_check = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key LIKE 'chapter_songs_%%_song' AND meta_value = %d",
    $current_id
));
if ($thread_check > 0) {
    $current_tier = 'thread';
}

// 2. Check Episode: Is this song referenced by any Fragment?
if ($current_tier === 'referenced') {
    $episode_check = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key LIKE 'fragment_songs_%%_song' AND meta_value = %d",
        $current_id
    ));
    if ($episode_check > 0) {
        $current_tier = 'episode';
    }
}

// 3. Check Strand: Is this song referenced by any Song-Based Element?
if ($current_tier === 'referenced') {
    $strand_check = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = 'related_content' AND meta_value = %d",
        $current_id
    ));
    if ($strand_check > 0) {
        $current_tier = 'strand';
    }
}

// 4. Check Featured: Is this song in the Listening Room?
if ($current_tier === 'referenced') {
    $lr_terms = get_the_terms($current_id, 'listening_room');
    if (!is_wp_error($lr_terms) && !empty($lr_terms)) {
        foreach ($lr_terms as $term) {
            if ($term->slug === 'listening-room') {
                $current_tier = 'featured';
                break;
            }
        }
    }
}

// ── GET ALL SONGS IN THIS TIER ──
$song_ids = [];

switch ($current_tier) {
    case 'thread':
        $song_ids = $wpdb->get_col(
            "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE 'chapter_songs_%%_song'"
        );
        break;

    case 'episode':
        $song_ids = $wpdb->get_col(
            "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE 'fragment_songs_%%_song'"
        );
        break;

    case 'strand':
        $song_ids = $wpdb->get_col(
            "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'related_content'"
        );
        break;

    case 'featured':
        $song_ids = get_posts([
            'post_type'      => 'song',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => [
                ['taxonomy' => 'listening_room', 'field' => 'slug', 'terms' => 'listening-room']
            ]
        ]);
        break;

    default: // referenced = everything not in the above
        $all_songs = get_posts(['post_type' => 'song', 'posts_per_page' => -1, 'fields' => 'ids']);
        $accounted = array_merge(
            $wpdb->get_col("SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE 'chapter_songs_%%_song'"),
            $wpdb->get_col("SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE 'fragment_songs_%%_song'"),
            $wpdb->get_col("SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'related_content'"),
            get_posts(['post_type' => 'song', 'posts_per_page' => -1, 'fields' => 'ids', 'tax_query' => [['taxonomy' => 'listening_room', 'field' => 'slug', 'terms' => 'listening-room']]])
        );
        $song_ids = array_diff($all_songs, array_unique($accounted));
        break;
}

// Convert to integers and sort by title
$song_ids = array_map('intval', array_unique(array_filter($song_ids)));
usort($song_ids, function($a, $b) {
    return strcmp(get_the_title($a), get_the_title($b));
});

// ── GET PREV/NEXT ──
$current_index = array_search($current_id, $song_ids);
$next_id = ($current_index !== false) ? ($song_ids[$current_index + 1] ?? null) : null;
$prev_id = ($current_index !== false) ? ($song_ids[$current_index - 1] ?? null) : null;

// ── LABEL ──
$labels = [
    'thread'     => 'Narrative Thread',
    'episode'    => 'Narrative Episode',
    'strand'     => 'Narrative Strand',
    'featured'   => 'Featured',
    'referenced' => 'Referenced'
];
$heading_prefix = ($labels[$current_tier] ?? 'Referenced') . ' Song';
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