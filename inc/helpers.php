<?php
// helpers.php — custom theme functions

function show_featured_in_threads($meta_key, $post_id = null) {
  if (!$post_id) {
    $post_id = get_the_ID();
  }

  $threads = get_posts([
    'post_type'      => ['chapter', 'fragment'],
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'meta_query'     => [
      [
        'key'     => $meta_key,              // e.g. 'books_cited', 'lyrics_cited'
        'value'   => '"' . $post_id . '"',   // exact match inside ACF’s serialized array
        'compare' => 'LIKE'
      ]
    ]
  ]);

  if ($threads): ?>
    <div class="narrative-threads" style="margin-top: 4em; text-align:center;">
      <h2>Featured In</h2>
      <div class="thread-grid">
        <?php foreach ($threads as $thread):
          $thumb = get_the_post_thumbnail_url($thread->ID, 'medium');
        ?>
          <div class="thread-item">
            <a href="<?php echo get_permalink($thread->ID); ?>">
              <?php if ($thumb): ?>
                <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($thread->ID)); ?>">
              <?php endif; ?>
              <h3><?php echo esc_html(get_the_title($thread->ID)); ?></h3>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif;
}


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
        'theme','topic','chapter','element', 'show'
    ];

    // Collect counts
    $type_counts = [];
    foreach ($key_cpts as $cpt) {
        if ($cpt === 'theme' || $cpt === 'topic') {
            $count = wp_count_terms($cpt, ['hide_empty' => false]);
        } else {
            $obj = wp_count_posts($cpt);
            $count = isset($obj->publish) ? $obj->publish : 0;
        }
        $type_counts[$cpt] = $count;
    }

    // Sort alphabetically by display title
    usort($key_cpts, function($a, $b) use ($map) {
        return strcasecmp($map[$a]['title'], $map[$b]['title']);
    });

    ob_start();
    ?>

    <!-- Outermost wrapper only centers the box -->
    <div style="display:flex; justify-content:center; width:100%; margin:0.5em 0;">

        <!-- White block (slimmer; centered) -->
        <div style="
            background-color:#ffffff;
            color:#111;
            padding:0.65em 0.75em;
            border-radius:6px;
            box-shadow:0 0 4px rgba(0,0,0,0.15);
            font-size:0.93em;
            line-height:1.35;
            width:65%;            /* <<< about 30% slimmer than full width */
            max-width:260px;      /* <<< prevents it from growing too wide */
            text-align:left;      /* <<< left-justify contents */
        ">

            <?php foreach ($key_cpts as $cpt) :
                $meta = $map[$cpt] ?? null;
                if (!$meta) continue;

                // Fix special homepage links
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
                    (<?php echo $type_counts[$cpt]; ?>)
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
