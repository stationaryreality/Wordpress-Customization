<?php
// helpers.php — custom theme functions



/* -------------------------------------------
 * SHORTCODE A: CPT Chart with emojis + counts
 * ------------------------------------------- */
function cpt_nav_chart_shortcode() {

    if (!function_exists('get_cpt_metadata')) {
        return "<!-- get_cpt_metadata missing -->";
    }

    $map = get_cpt_metadata();

    $key_cpts = [
        'artist','book','concept','excerpt','fragment','image','lyric',
        'movie','organization','portal','profile','quote','song',
        'theme','topic','chapter','element','show', 'game', 'video'
    ];

    // Collect counts
    $type_counts = [];
    $grand_total = 0;

    foreach ($key_cpts as $cpt) {
        if ($cpt === 'theme' || $cpt === 'topic') {
            $count = wp_count_terms($cpt, ['hide_empty' => false]);
        } else {
            $obj = wp_count_posts($cpt);
            $count = isset($obj->publish) ? $obj->publish : 0;
        }

        $type_counts[$cpt] = $count;
        $grand_total += $count;
    }

    // Sort alphabetically by display title
    usort($key_cpts, function($a, $b) use ($map) {
        return strcasecmp($map[$a]['title'], $map[$b]['title']);
    });

    ob_start();
    ?>

    <div style="display:flex; justify-content:center; width:100%; margin:0.5em 0;">

        <div style="
            background-color:#ffffff;
            color:#111;
            padding:0.65em 0.75em;
            border-radius:6px;
            box-shadow:0 0 4px rgba(0,0,0,0.15);
            font-size:0.93em;
            line-height:1.35;
            width:65%;
            max-width:260px;
            text-align:left;
        ">

            <!-- Grand Total -->
            <div style="font-weight:bold; margin-bottom:6px;">
                Total Entries: <?php echo number_format($grand_total); ?>
            </div>

            <div style="border-top:1px solid #ddd; margin:6px 0;"></div>

            <?php foreach ($key_cpts as $cpt) :
                $meta = $map[$cpt] ?? null;
                if (!$meta) continue;

                $link = $meta['link'];
                if ($cpt === 'chapter')   $link = '/narrative-threads';
                if ($cpt === 'fragment') $link = '/narrative-episodes';
                ?>

                <div style="margin:2px 0;">
                    <span><?php echo $meta['emoji']; ?></span>
                    <a href="<?php echo esc_url(home_url($link)); ?>" 
                       style="text-decoration:none; color:#111; font-weight:bold;">
                        <?php echo esc_html($meta['title']); ?>
                    </a>
                    (<?php echo number_format($type_counts[$cpt]); ?>)
                </div>

            <?php endforeach; ?>

        </div>
    </div>

    <?php
    return ob_get_clean();
}

add_shortcode('cpt_nav_chart', 'cpt_nav_chart_shortcode');

/* -------------------------------------------
 * SHORTCODE B: Last Updated Date
 * ------------------------------------------- */
function site_last_updated_shortcode() {

    // Pull latest modified post from all CPTs you have registered in the map
    if (!function_exists('get_cpt_metadata')) {
        return "<!-- get_cpt_metadata missing -->";
    }

    $map = get_cpt_metadata();
    $types = array_keys($map);

    $latest = get_posts([
        'post_type'      => $types,
        'posts_per_page' => 1,
        'orderby'        => 'modified',
        'order'          => 'DESC',
        'post_status'    => 'publish'
    ]);

    $updated = $latest ? get_the_modified_date('M j, Y', $latest[0]->ID) : '';

    return '<div style="font-size:12px; text-align:center; margin-top:6px;">Site Updated: ' . esc_html($updated) . '</div>';
}

add_shortcode('site_last_updated', 'site_last_updated_shortcode');

function get_content_hub_override($post_id = 0) {

    $post_id = $post_id ?: get_the_ID();

    switch (get_post_type($post_id)) {

        case 'artist':

            if (has_term('rapper', 'artist_type', $post_id)) {
                return [
                    'title' => 'Rap Pages',
                    'url'   => home_url('/rap-pages/')
                ];
            }

            break;

        case 'song':

            if (has_term('rap', 'song_category', $post_id)) {
                return [
                    'title' => 'Rap Pages',
                    'url'   => home_url('/rap-pages/')
                ];
            }

            break;

        case 'lyric':

            if (has_term('rap', 'song_category', $post_id)) {
                return [
                    'title' => 'Rap Pages',
                    'url'   => home_url('/rap-pages/')
                ];
            }

            break;
    }

    return false;
}

/**
 * Determine whether a taxonomy term is a system-only term.
 */
function kp_is_system_term( $term ) {

    if ( empty( $term ) || empty( $term->slug ) ) {
        return false;
    }

    return strpos( $term->slug, 'top-' ) === 0;
}

/**
 * Remove system terms from a term array.
 */
function kp_filter_system_terms( $terms ) {

    if ( empty( $terms ) || ! is_array( $terms ) ) {
        return [];
    }

    return array_filter( $terms, function( $term ) {
        return ! kp_is_system_term( $term );
    });
}


/**
 * UNIVERSAL PRIMARY SONGS GETTER
 * Finds the primary song(s) for Chapters, Fragments, and Elements.
 */
function kp_get_primary_songs($post_id) {
    $post_type = get_post_type($post_id);
    $primary_songs = [];

    // 1. CONTEXT CHECK (For Elements attached to Chapters/Fragments)
    if ($post_type === 'element' && function_exists('kp_get_element_primary_song')) {
        $context_song = kp_get_element_primary_song($post_id);
        if ($context_song instanceof WP_Post) {
            $primary_songs[] = $context_song;
        }
    }

    // 2. ELEMENT LOGIC (Song Based -> related_content)
    if ($post_type === 'element') {
        $terms = get_the_terms($post_id, 'strand_type');
        $is_song_based = false;
        
        if (!is_wp_error($terms) && !empty($terms)) {
            foreach ($terms as $term) {
                if ($term->slug === 'song-based') {
                    $is_song_based = true;
                    break;
                }
            }
        }

        // If it is Song Based, check related_content for the first song
        if ($is_song_based) {
            $related = get_field('related_content', $post_id);
            if (!empty($related) && is_array($related)) {
                foreach ($related as $item) {
                    if ($item instanceof WP_Post && $item->post_type === 'song') {
                        $primary_songs[] = $item;
                        break; // Stop at the first one found
                    }
                }
            }
        }
    } 
    
    // 3. CHAPTER & FRAGMENT LOGIC (Repeater -> role=primary)
    else {
        $songs = get_field('chapter_songs', $post_id);
        if (!empty($songs) && is_array($songs)) {
            foreach ($songs as $row) {
                if (!empty($row['role']) && $row['role'] === 'primary' && !empty($row['song']) && $row['song'] instanceof WP_Post) {
                    $primary_songs[] = $row['song'];
                }
            }
        }
    }

    // SAFE DEDUPLICATION (Replaces array_unique to prevent PHP 8 crashes)
    $unique_songs = [];
    $seen_ids = [];
    foreach ($primary_songs as $song) {
        if (!in_array($song->ID, $seen_ids)) {
            $unique_songs[] = $song;
            $seen_ids[] = $song->ID;
        }
    }
    
    return $unique_songs;
}

/**
 * UNIVERSAL ARTIST HEADER RENDERER
 * Handles arrays and correctly displays "doubles" side-by-side.
 */
function kp_render_artist_header($primary_songs) { 
    if (empty($primary_songs) || !is_array($primary_songs)) return;

    echo '<div class="kp-artist-meta-wrapper">';

    foreach ($primary_songs as $primary_song) {
        if (!$primary_song instanceof WP_Post) continue;

        $artist_field = get_field('song_artist', $primary_song->ID);
        $primary_artist = $artist_field ? get_post($artist_field) : null;

        if (!$primary_artist instanceof WP_Post) continue;

        $portrait    = get_field('portrait_image', $primary_artist->ID);
        $img_url     = $portrait ? $portrait['sizes']['thumbnail'] : '';
        $artist_name = get_the_title($primary_artist->ID);
        $artist_link = get_permalink($primary_artist->ID);
        
        $song_title = get_the_title($primary_song->ID);
        $song_link  = get_permalink($primary_song->ID);
        ?>
        <div class="kp-artist-meta">
            <?php if ($img_url): ?>
                <a href="<?php echo esc_url($artist_link); ?>" class="kp-artist-link">
                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($artist_name); ?>" class="kp-artist-thumbnail">
                </a>
            <?php endif; ?>

            <h2 class="kp-artist-name">
                <a href="<?php echo esc_url($artist_link); ?>" class="kp-artist-link">
                    <?php echo esc_html($artist_name); ?>
                </a>
            </h2>

            <div class="kp-song-title">
                <a href="<?php echo esc_url($song_link); ?>" class="kp-song-link">
                    <?php echo esc_html($song_title); ?>
                </a>
            </div>
        </div>
        <?php
    }
    echo '</div>';
}

/**
 * Builds lyric attribution string.
 */
function kp_lyric_attribution($lyric_id) {
    $song = get_field('song', $lyric_id);
    if (!$song) return '';
    $song_id    = is_object($song) ? $song->ID : $song;
    $song_title = get_the_title($song_id);
    $song_url   = get_permalink($song_id);

    // Primary artist
    $primary = get_field('song_artist', $song_id);
    $primary_id = $primary ? (is_object($primary) ? $primary->ID : $primary) : 0;

    // Check if lyric_artist is explicitly set
    $lyric_artists = get_field('lyric_artist', $lyric_id);
    $has_explicit_speaker = !empty($lyric_artists) && is_array($lyric_artists);

    // ── CASE 1: lyric_artist is populated → use "verse by X featuring Y" ──
    if ($has_explicit_speaker) {
        $speaker_ids = [];
        foreach ($lyric_artists as $la) {
            $speaker_ids[] = is_object($la) ? $la->ID : $la;
        }

        // Build ordered track artist list: primary + additionals
        $track_ids = [];
        if ($primary) $track_ids[] = $primary_id;
        $additionals = get_field('additional_artists', $song_id);
        if (!empty($additionals)) {
            foreach ($additionals as $add) {
                $aid = is_object($add) ? $add->ID : $add;
                if (!in_array($aid, $track_ids)) $track_ids[] = $aid;
            }
        }

        // Speakers with links
        $speaker_parts = [];
        foreach ($speaker_ids as $sid) {
            $speaker_parts[] = '<a href="' . esc_url(get_permalink($sid)) . '">' . esc_html(get_the_title($sid)) . '</a>';
        }

        // Featuring: everyone on track except the speakers
        $feat_parts = [];
        foreach ($track_ids as $tid) {
            if (!in_array($tid, $speaker_ids)) {
                $feat_parts[] = '<a href="' . esc_url(get_permalink($tid)) . '">' . esc_html(get_the_title($tid)) . '</a>';
            }
        }

        $out = 'Song: <a href="' . esc_url($song_url) . '">' . esc_html($song_title) . '</a> verse by ' . implode(', ', $speaker_parts);
        if (count($speaker_parts) > 1) {
            $last = array_pop($speaker_parts);
            $out = 'Song: <a href="' . esc_url($song_url) . '">' . esc_html($song_title) . '</a> verse by ' . implode(', ', $speaker_parts) . ' & ' . $last;
        }
        if (!empty($feat_parts)) {
            if (count($feat_parts) === 1) {
                $out .= ' featuring ' . $feat_parts[0];
            } else {
                $last = array_pop($feat_parts);
                $out .= ' featuring ' . implode(', ', $feat_parts) . ' & ' . $last;
            }
        }
        return $out;
    }

    // ── CASE 2: lyric_artist is blank → use simple "Song: X by Y" ──
    if ($primary) {
        $artist_name = get_the_title($primary_id);
        $artist_url  = get_permalink($primary_id);
        return 'Song: <a href="' . esc_url($song_url) . '">' . esc_html($song_title) . '</a> by <a href="' . esc_url($artist_url) . '">' . esc_html($artist_name) . '</a>';
    }

    // Fallback: just the song link
    return 'Song: <a href="' . esc_url($song_url) . '">' . esc_html($song_title) . '</a>';
}


/**
 * Gets all tiers a song or artist belongs to.
 * Priority: thread > episode > strand > featured > referenced
 */
function kp_get_tiers($post_id) {
    static $cache = [];
    if (isset($cache[$post_id])) {
        return $cache[$post_id];
    }

    $post_type = get_post_type($post_id);
    $tiers = [];

    // ── SONG TIERS ──
    if ($post_type === 'song') {
        // 1. Thread (Chapters)
        $chapters = get_posts(['post_type' => 'chapter', 'posts_per_page' => -1, 'fields' => 'ids']);
        foreach ($chapters as $cid) {
            foreach (kp_get_primary_songs($cid) as $song) {
                if ($song->ID == $post_id) {
                    $tiers[] = 'thread';
                    break 2;
                }
            }
        }

        // 2. Episode (Fragments)
        $fragments = get_posts(['post_type' => 'fragment', 'posts_per_page' => -1, 'fields' => 'ids']);
        foreach ($fragments as $fid) {
            foreach (kp_get_primary_songs($fid) as $song) {
                if ($song->ID == $post_id) {
                    $tiers[] = 'episode';
                    break 2;
                }
            }
        }

        // 3. Strand (Song-Based Elements)
        $elements = get_posts(['post_type' => 'element', 'posts_per_page' => -1, 'fields' => 'ids']);
        foreach ($elements as $eid) {
            $terms = get_the_terms($eid, 'strand_type');
            if (!is_wp_error($terms) && !empty($terms)) {
                foreach ($terms as $term) {
                    if ($term->slug === 'song-based') {
                        foreach (kp_get_primary_songs($eid) as $song) {
                            if ($song->ID == $post_id) {
                                $tiers[] = 'strand';
                                break 3;
                            }
                        }
                    }
                }
            }
        }

        // 4. Featured (Listening Room)
        $terms = get_the_terms($post_id, 'listening_room');
        if (!is_wp_error($terms) && !empty($terms)) {
            foreach ($terms as $term) {
                if ($term->slug === 'listening-room') {
                    $tiers[] = 'featured';
                    break;
                }
            }
        }

        // 5. Referenced (Fallback)
        if (empty($tiers)) {
            $tiers[] = 'referenced';
        }

        $tiers = array_unique($tiers);
        $cache[$post_id] = $tiers;
        return $tiers;
    }

    // ── ARTIST TIERS ──
    if ($post_type === 'artist') {
        $artist_songs = get_posts([
            'post_type'      => 'song',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => [
                'relation' => 'OR',
                ['key' => 'song_artist', 'value' => $post_id, 'compare' => '='],
                ['key' => 'additional_artists', 'value' => $post_id, 'compare' => 'LIKE']
            ]
        ]);

        $artist_tiers = [];
        foreach ($artist_songs as $song_id) {
            $song_tiers = kp_get_tiers($song_id); // Uses cache
            foreach ($song_tiers as $t) {
                if (!in_array($t, $artist_tiers)) {
                    $artist_tiers[] = $t;
                }
            }
        }

        if (empty($artist_tiers)) {
            $artist_tiers[] = 'referenced';
        }

        // Sort by priority
        $priority = ['thread', 'episode', 'strand', 'featured', 'referenced'];
        usort($artist_tiers, function($a, $b) use ($priority) {
            return array_search($a, $priority) <=> array_search($b, $priority);
        });

        $artist_tiers = array_unique($artist_tiers);
        $cache[$post_id] = $artist_tiers;
        return $artist_tiers;
    }

    $cache[$post_id] = ['referenced'];
    return ['referenced'];
}

/**
 * Gets the label for a tier
 */
function kp_get_tier_label($tier) {
    $labels = [
        'thread'     => 'Narrative Thread',
        'episode'    => 'Narrative Episode',
        'strand'     => 'Narrative Strand',
        'featured'   => 'Featured',
        'referenced' => 'Referenced'
    ];
    return $labels[$tier] ?? 'Referenced';
}

/**
 * Gets all posts that belong to a specific tier (allows duplicates)
 */
function kp_get_tier_posts($tier, $post_type) {
    $all_posts = get_posts([
        'post_type'      => $post_type,
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'fields'         => 'ids'
    ]);
    
    $tier_posts = [];
    foreach ($all_posts as $pid) {
        $tiers = kp_get_tiers($pid);
        if (in_array($tier, $tiers)) {
            $tier_posts[] = $pid;
        }
    }
    return $tier_posts;
}


require_once get_stylesheet_directory() . '/inc/helpers/wikipedia.php';

require_once get_stylesheet_directory() . '/inc/helpers/featured-in.php';