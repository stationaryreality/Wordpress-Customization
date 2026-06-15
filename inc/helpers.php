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
        'movie','organization','profile','quote','reference','song',
        'theme','topic','chapter','element','show', 'game'
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

require_once get_stylesheet_directory() . '/inc/helpers/wikipedia.php';

require_once get_stylesheet_directory() . '/inc/helpers/featured-in.php';