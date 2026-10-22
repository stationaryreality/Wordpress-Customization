<?php
$current_id = get_the_ID();

// ── Song tier builder (same as song nav, guarded against re-declaration) ──
if (!function_exists('kp_nav_song_tiers')) {
    function kp_nav_song_tiers() {
        static $tiers = null;
        if ($tiers !== null) return $tiers;

        $tiers = ['thread'=>[], 'episode'=>[], 'strand'=>[], 'featured'=>[], 'referenced'=>[]];

        // Thread (Chapters)
        $chapters = get_posts(['post_type'=>'chapter','posts_per_page'=>-1,'fields'=>'ids']);
        foreach ($chapters as $cid) {
            foreach (kp_get_primary_songs($cid) as $song) {
                if (!in_array($song->ID, $tiers['thread'])) $tiers['thread'][] = $song->ID;
            }
        }

        // Episode (Fragments)
        $fragments = get_posts(['post_type'=>'fragment','posts_per_page'=>-1,'fields'=>'ids']);
        foreach ($fragments as $fid) {
            foreach (kp_get_primary_songs($fid) as $song) {
                if (!in_array($song->ID, $tiers['episode'])) $tiers['episode'][] = $song->ID;
            }
        }

        // Strand (Song-Based Elements)
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

        // Featured (Listening Room)
        $tiers['featured'] = get_posts([
            'post_type'      => 'song',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => [
                ['taxonomy'=>'listening_room','field'=>'slug','terms'=>'listening-room']
            ]
        ]);

        // Referenced = everything else
        $all_songs = get_posts(['post_type'=>'song','posts_per_page'=>-1,'fields'=>'ids']);
        $accounted = array_unique(array_merge(
            $tiers['thread'], $tiers['episode'], $tiers['strand'], $tiers['featured']
        ));
        $tiers['referenced'] = array_values(array_diff($all_songs, $accounted));

        return $tiers;
    }
}

// ── Artist tier builder: derives each artist's tier from their songs ──
if (!function_exists('kp_nav_artist_tiers')) {
    function kp_nav_artist_tiers() {
        static $artist_tiers = null;
        if ($artist_tiers !== null) return $artist_tiers;

        $song_tiers = kp_nav_song_tiers();
        $priority   = ['thread','episode','strand','featured','referenced'];

        $artist_tiers = ['thread'=>[], 'episode'=>[], 'strand'=>[], 'featured'=>[], 'referenced'=>[]];
        $assigned     = [];

        // Walk songs in priority order; first tier an artist appears in = their highest tier
        foreach ($priority as $tier_name) {
            if ($tier_name === 'referenced') continue; // handled below
            foreach ($song_tiers[$tier_name] as $song_id) {
                $primary    = get_field('song_artist', $song_id);
                $additional = get_field('additional_artists', $song_id);

                $artist_ids = [];
                if ($primary) {
                    $artist_ids[] = is_object($primary) ? $primary->ID : $primary;
                }
                if (!empty($additional)) {
                    foreach ($additional as $a) {
                        $aid = is_object($a) ? $a->ID : $a;
                        if (!in_array($aid, $artist_ids)) $artist_ids[] = $aid;
                    }
                }

                foreach ($artist_ids as $aid) {
                    if (!isset($assigned[$aid])) {
                        $assigned[$aid] = true;
                        $artist_tiers[$tier_name][] = $aid;
                    }
                }
            }
        }

        // Artists with no songs, or only referenced-tier songs → referenced
        $all_artists = get_posts(['post_type'=>'artist','posts_per_page'=>-1,'fields'=>'ids']);
        foreach ($all_artists as $aid) {
            if (!isset($assigned[$aid])) {
                $artist_tiers['referenced'][] = $aid;
            }
        }

        return $artist_tiers;
    }
}

$artist_tiers = kp_nav_artist_tiers();

// ── Determine current artist's highest tier ──
$priority = ['thread','episode','strand','featured','referenced'];
$current_tier = 'referenced';
foreach ($priority as $t) {
    if (in_array($current_id, $artist_tiers[$t])) {
        $current_tier = $t;
        break;
    }
}

// ── Sorted artist list for this tier ──
$artist_ids = $artist_tiers[$current_tier];
usort($artist_ids, function($a, $b) {
    return strcmp(get_the_title($a), get_the_title($b));
});

// ── Prev / Next ──
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