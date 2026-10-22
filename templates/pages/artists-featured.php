<?php
/* Template Name: Artists Featured */
get_header();
?>

<main id="primary" class="site-main page-artists-featured">
    <?php
    
    // ──────────────────────────────────────────────
    // 1. COLLECT SONG IDs (Same logic as Songs page)
    // ──────────────────────────────────────────────
    
    $thread_song_ids  = [];
    $episode_song_ids = [];
    $strand_song_ids  = [];

    // A. Chapters (Narrative Thread)
    $chapters = get_posts(['post_type' => 'chapter', 'posts_per_page' => -1, 'fields' => 'ids']);
    foreach ($chapters as $cid) {
        foreach (kp_get_primary_songs($cid) as $song) $thread_song_ids[] = $song->ID;
    }

    // B. Fragments (Narrative Episode)
    $fragments = get_posts(['post_type' => 'fragment', 'posts_per_page' => -1, 'fields' => 'ids']);
    foreach ($fragments as $fid) {
        foreach (kp_get_primary_songs($fid) as $song) $episode_song_ids[] = $song->ID;
    }

    // C. Elements (Narrative Strand)
    $elements = get_posts(['post_type' => 'element', 'posts_per_page' => -1, 'fields' => 'ids']);
    foreach ($elements as $eid) {
        $terms = get_the_terms($eid, 'strand_type');
        if (!is_wp_error($terms) && !empty($terms)) {
            foreach ($terms as $term) {
                if ($term->slug === 'song-based') {
                    foreach (kp_get_primary_songs($eid) as $song) $strand_song_ids[] = $song->ID;
                    break;
                }
            }
        }
    }

    // D. Featured Songs (Listening Room)
    $featured_query = new WP_Query([
        'post_type'      => 'song',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => [['taxonomy' => 'listening_room', 'field' => 'slug', 'terms' => 'listening-room']],
    ]);
    $featured_song_ids = $featured_query->posts;
    wp_reset_postdata();


    // ──────────────────────────────────────────────
    // 2. CONVERT SONG IDs TO ARTIST IDs
    // ──────────────────────────────────────────────
    
    function lr_extract_artists($song_ids) {
        $artist_ids = [];
        foreach ($song_ids as $song_id) {
            $artist_field = get_field('song_artist', $song_id);
            if ($artist_field) {
                $artist_post = get_post($artist_field);
                // Ensure it's a valid Artist post type
                if ($artist_post instanceof WP_Post && $artist_post->post_type === 'artist') {
                    $artist_ids[] = $artist_post->ID;
                }
            }
        }
        return array_unique(array_filter($artist_ids));
    }

    $thread_artist_ids   = lr_extract_artists($thread_song_ids);
    $episode_artist_ids  = lr_extract_artists($episode_song_ids);
    $strand_artist_ids   = lr_extract_artists($strand_song_ids);
    $featured_artist_ids = lr_extract_artists($featured_song_ids);


    // ──────────────────────────────────────────────
    // 3. REFERENCED ARTISTS = EVERYTHING ELSE
    // ──────────────────────────────────────────────
    
    $all_artist_ids = get_posts(['post_type' => 'artist', 'posts_per_page' => -1, 'fields' => 'ids']);
    $accounted_artist_ids = array_merge($thread_artist_ids, $episode_artist_ids, $strand_artist_ids, $featured_artist_ids);
    $referenced_artist_ids = array_diff($all_artist_ids, array_unique($accounted_artist_ids));


    // ──────────────────────────────────────────────
    // 4. EXCLUDE RAPPERS (Matching your original code)
    // ──────────────────────────────────────────────
    
    $rappers = get_posts([
        'post_type'      => 'artist',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => [['taxonomy' => 'artist_type', 'field' => 'slug', 'terms' => 'rapper']]
    ]);

    if (!empty($rappers)) {
        $thread_artist_ids     = array_diff($thread_artist_ids, $rappers);
        $episode_artist_ids    = array_diff($episode_artist_ids, $rappers);
        $strand_artist_ids     = array_diff($strand_artist_ids, $rappers);
        $featured_artist_ids   = array_diff($featured_artist_ids, $rappers);
        $referenced_artist_ids = array_diff($referenced_artist_ids, $rappers);
    }


    // ──────────────────────────────────────────────
    // 5. RENDER SECTIONS USING YOUR EXISTING GRID
    // ──────────────────────────────────────────────
    
    function lr_render_artist_section($title, $artist_ids) {
        if (empty($artist_ids)) return;

        $artist_query = new WP_Query([
            'post_type'      => 'artist',
            'posts_per_page' => -1,
            'post__in'       => $artist_ids,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);

        if ($artist_query->have_posts()) {
            ?>
            <div class="cpt-artist-feature-group">
                <h3 class="cpt-artist-feature-level"><?php echo esc_html($title); ?></h3>
                <?php
                // Pass the query to your existing artist grid template part
                set_query_var('artist_query', $artist_query);
                get_template_part('template-parts/grids/artist');
                ?>
            </div>
            <?php
            wp_reset_postdata();
        }
    }

    // Render in order
    lr_render_artist_section('📖 Narrative Thread Artists', $thread_artist_ids);
    lr_render_artist_section('🎬 Narrative Episode Artists', $episode_artist_ids);
    lr_render_artist_section('🧵 Narrative Strand Artists', $strand_artist_ids);
    lr_render_artist_section('🎧 Featured Artists', $featured_artist_ids);
    lr_render_artist_section('🎤 Referenced Artists', $referenced_artist_ids);
    ?>
</main>

<?php get_footer(); ?>