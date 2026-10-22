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
 * Mutually-exclusive SONG tiers.
 * Each song appears in exactly ONE tier (highest priority wins).
 * Priority: thread > episode > strand > featured > referenced
 */
function kp_get_song_tiers() {
    static $tiers = null;
    if ($tiers !== null) return $tiers;

    $tiers = ['thread'=>[], 'episode'=>[], 'strand'=>[], 'featured'=>[], 'referenced'=>[]];
    $assigned = [];

    // THREAD (Chapters) — highest priority
    $chapters = get_posts(['post_type'=>'chapter','posts_per_page'=>-1,'fields'=>'ids']);
    foreach ($chapters as $cid) {
        foreach (kp_get_primary_songs($cid) as $song) {
            if (!isset($assigned[$song->ID])) {
                $assigned[$song->ID] = true;
                $tiers['thread'][] = $song->ID;
            }
        }
    }

    // EPISODE (Fragments) — skip songs already in Thread
    $fragments = get_posts(['post_type'=>'fragment','posts_per_page'=>-1,'fields'=>'ids']);
    foreach ($fragments as $fid) {
        foreach (kp_get_primary_songs($fid) as $song) {
            if (!isset($assigned[$song->ID])) {
                $assigned[$song->ID] = true;
                $tiers['episode'][] = $song->ID;
            }
        }
    }

    // STRAND (Song-Based Elements) — skip songs already in Thread/Episode
    $elements = get_posts(['post_type'=>'element','posts_per_page'=>-1,'fields'=>'ids']);
    foreach ($elements as $eid) {
        $terms = get_the_terms($eid, 'strand_type');
        if (!is_wp_error($terms) && !empty($terms)) {
            foreach ($terms as $term) {
                if ($term->slug === 'song-based') {
                    foreach (kp_get_primary_songs($eid) as $song) {
                        if (!isset($assigned[$song->ID])) {
                            $assigned[$song->ID] = true;
                            $tiers['strand'][] = $song->ID;
                        }
                    }
                    break;
                }
            }
        }
    }

    // FEATURED (Listening Room) — skip songs already in higher tiers
    $featured = get_posts([
        'post_type'=>'song','posts_per_page'=>-1,'fields'=>'ids',
        'tax_query'=>[['taxonomy'=>'listening_room','field'=>'slug','terms'=>'listening-room']]
    ]);
    foreach ($featured as $song_id) {
        if (!isset($assigned[$song_id])) {
            $assigned[$song_id] = true;
            $tiers['featured'][] = $song_id;
        }
    }

    // REFERENCED = everything not assigned above
    $all_songs = get_posts(['post_type'=>'song','posts_per_page'=>-1,'fields'=>'ids']);
    foreach ($all_songs as $song_id) {
        if (!isset($assigned[$song_id])) {
            $tiers['referenced'][] = $song_id;
        }
    }

    return $tiers;
}

/**
 * Mutually-exclusive ARTIST tiers, derived from song tiers.
 * Each artist appears in exactly ONE tier (the highest tier of any of their songs).
 */
function kp_get_artist_tiers() {
    static $artist_tiers = null;
    if ($artist_tiers !== null) return $artist_tiers;

    $song_tiers = kp_get_song_tiers();
    $priority   = ['thread','episode','strand','featured','referenced'];

    $artist_tiers = ['thread'=>[], 'episode'=>[], 'strand'=>[], 'featured'=>[], 'referenced'=>[]];
    $assigned = [];

    // Walk tiers in priority order; first tier an artist appears in = their tier
    foreach ($priority as $tier_name) {
        if ($tier_name === 'referenced') continue; // handled by fallback below
        foreach ($song_tiers[$tier_name] as $song_id) {
            $primary    = get_field('song_artist', $song_id);
            $additional = get_field('additional_artists', $song_id);

            $artist_ids = [];
            if ($primary) $artist_ids[] = is_object($primary) ? $primary->ID : $primary;
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

    // Artists with no songs, or only referenced songs → referenced
    $all_artists = get_posts(['post_type'=>'artist','posts_per_page'=>-1,'fields'=>'ids']);
    foreach ($all_artists as $aid) {
        if (!isset($assigned[$aid])) {
            $artist_tiers['referenced'][] = $aid;
        }
    }

    return $artist_tiers;
}


require_once get_stylesheet_directory() . '/inc/helpers/wikipedia.php';

require_once get_stylesheet_directory() . '/inc/helpers/featured-in.php';