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
 * 1. UNIVERSAL PRIMARY SONG GETTER
 * Determines the primary song for Chapters, Fragments, and Elements.
 */
function kp_get_primary_song($post_id) {
    $post_type = get_post_type($post_id);
    
    // A. If it's an Element, try the old context logic first (Attached Chapter/Fragment)
    if ($post_type === 'element' && function_exists('kp_get_element_primary_song')) {
        $context_song = kp_get_element_primary_song($post_id);
        if ($context_song instanceof WP_Post) {
            return $context_song;
        }
    }

    // B. Check the ACF Repeater field 
    // (Assuming all 3 CPTs use the same 'chapter_songs' repeater field name)
    $songs = get_field('chapter_songs', $post_id);

    if (!empty($songs) && is_array($songs)) {
        // Look for explicitly marked 'primary' role
        foreach ($songs as $row) {
            if (!empty($row['role']) && $row['role'] === 'primary' && !empty($row['song']) && $row['song'] instanceof WP_Post) {
                return $row['song'];
            }
        }

        // Fallback for "Song Based" Elements: take the FIRST song in the array
        if ($post_type === 'element') {
            $terms = get_the_terms($post_id, 'strand_type'); 
            if (!is_wp_error($terms) && !empty($terms)) {
                foreach ($terms as $term) {
                    if ($term->slug === 'song-based') { 
                        $first_song = $songs[0]['song'];
                        if ($first_song instanceof WP_Post) {
                            return $first_song;
                        }
                    }
                }
            }
        }
    }

    return null;
}

/**
 * 2. UNIVERSAL ARTIST HEADER RENDERER
 * Outputs the clean HTML block for the primary song.
 */
function kp_render_artist_header($primary_song) {
    if (!$primary_song instanceof WP_Post) return;

    $artist_field = get_field('song_artist', $primary_song->ID);
    $primary_artist = $artist_field ? get_post($artist_field) : null;

    if (!$primary_artist instanceof WP_Post) return;

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

require_once get_stylesheet_directory() . '/inc/helpers/wikipedia.php';

require_once get_stylesheet_directory() . '/inc/helpers/featured-in.php';