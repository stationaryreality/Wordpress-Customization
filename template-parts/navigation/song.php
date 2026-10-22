<?php
$current_id = get_the_ID();

// Build tier lists once per request (guarded against re-declaration)
if (!function_exists('kp_nav_song_tiers')) {
    function kp_nav_song_tiers() {
        static $tiers = null;
        if ($tiers !== null) return $tiers;

        $tiers = ['thread'=>[], 'episode'=>[], 'strand'=>[], 'featured'=>[], 'referenced'=>[]];

        // ── Thread (Chapters) ──
        $chapters = get_posts(['post_type'=>'chapter','posts_per_page'=>-1,'fields'=>'ids']);
        foreach ($chapters as $cid) {
            foreach (kp_get_primary_songs($cid) as $song) {
                if (!in_array($song->ID, $tiers['thread'])) $tiers['thread'][] = $song->ID;
            }
        }

        // ── Episode (Fragments) ──
        $fragments = get_posts(['post_type'=>'fragment','posts_per_page'=>-1,'fields'=>'ids']);
        foreach ($fragments as $fid) {
            foreach (kp_get_primary_songs($fid) as $song) {
                if (!in_array($song->ID, $tiers['episode'])) $tiers['episode'][] = $song->ID;
            }
        }

        // ── Strand (Song-Based Elements) ──
        $elements = get_posts(['post_type'=>'element','posts_per_page'=>-1,'fields'=>'ids']);
        foreach ($elements as $eid) {
            $terms = get_the_terms($eid, 'strand_type');
            if (!is_wp_error($terms) && !empty($terms)) {
                foreach ($terms as $term) {
                    if ($term->slug === 'song-based') {
                        foreach (kp_get_primary_songs($eid) as $song) {
                            if (!in_array($song->ID, $tiers['strand'])) $tiers['strand'][] = $song->ID;
                        }
                        break;
                    }
                }
            }
        }

        // ── Featured (Listening Room) ──
        $tiers['featured'] = get_posts([
            'post_type'      => 'song',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => [
                ['taxonomy'=>'listening_room','field'=>'slug','terms'=>'listening-room']
            ]
        ]);

        // ── Referenced = everything not in the above ──
        $all_songs = get_posts(['post_type'=>'song','posts_per_page'=>-1,'fields'=>'ids']);
        $accounted = array_unique(array_merge(
            $tiers['thread'], $tiers['episode'], $tiers['strand'], $tiers['featured']
        ));
        $tiers['referenced'] = array_values(array_diff($all_songs, $accounted));

        return $tiers;
    }
}

$tiers = kp_nav_song_tiers();

// ── Determine current song's highest-priority tier ──
$priority = ['thread','episode','strand','featured','referenced'];
$current_tier = 'referenced';
foreach ($priority as $t) {
    if (in_array($current_id, $tiers[$t])) {
        $current_tier = $t;
        break;
    }
}

// ── Sorted list for this tier ──
$song_ids = $tiers[$current_tier];
usort($song_ids, function($a, $b) {
    return strcmp(get_the_title($a), get_the_title($b));
});

// ── Prev / Next ──
$current_index = array_search($current_id, $song_ids);
$next_id = ($current_index !== false) ? ($song_ids[$current_index + 1] ?? null) : null;
$prev_id = ($current_index !== false) ? ($song_ids[$current_index - 1] ?? null) : null;

$labels = [
    'thread'     => 'Narrative Thread',
    'episode'    => 'Narrative Episode',
    'strand'     => 'Narrative Strand',
    'featured'   => 'Featured',
    'referenced' => 'Referenced'
];
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