<?php
/* Template Name: Songs Featured */
get_header();
?>

<main id="primary" class="site-main cpt-song-archive">
    <section class="cpt-song-section">

        <?php
        // ──────────────────────────────────────────────
        // 1. COLLECT SONG IDs FROM YOUR CPTs
        // ──────────────────────────────────────────────

        $thread_ids  = [];
        $episode_ids = [];
        $strand_ids  = [];

        // A. Narrative Thread Songs (From Chapters)
        $chapters = get_posts(['post_type' => 'chapter', 'posts_per_page' => -1, 'fields' => 'ids']);
        foreach ($chapters as $cid) {
            $songs = kp_get_primary_songs($cid);
            foreach ($songs as $song) {
                $thread_ids[] = $song->ID;
            }
        }

        // B. Narrative Episode Songs (From Fragments)
        $fragments = get_posts(['post_type' => 'fragment', 'posts_per_page' => -1, 'fields' => 'ids']);
        foreach ($fragments as $fid) {
            $songs = kp_get_primary_songs($fid);
            foreach ($songs as $song) {
                $episode_ids[] = $song->ID;
            }
        }

        // C. Narrative Strand Songs (From Song-Based Elements)
        $elements = get_posts(['post_type' => 'element', 'posts_per_page' => -1, 'fields' => 'ids']);
        foreach ($elements as $eid) {
            $terms = get_the_terms($eid, 'strand_type');
            if (!is_wp_error($terms) && !empty($terms)) {
                foreach ($terms as $term) {
                    if ($term->slug === 'song-based') {
                        $songs = kp_get_primary_songs($eid);
                        foreach ($songs as $song) {
                            $strand_ids[] = $song->ID;
                        }
                        break;
                    }
                }
            }
        }

        // Clean up duplicates
        $thread_ids  = array_unique(array_filter($thread_ids));
        $episode_ids = array_unique(array_filter($episode_ids));
        $strand_ids  = array_unique(array_filter($strand_ids));

        // D. Featured Songs (Everything in the Listening Room)
        $featured_query = new WP_Query([
            'post_type'      => 'song',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => [
                [
                    'taxonomy' => 'listening_room', // Make sure this matches your exact taxonomy name
                    'field'    => 'slug',
                    'terms'    => 'listening-room',
                ],
            ],
        ]);
        $featured_ids = $featured_query->posts;
        wp_reset_postdata();


        // ──────────────────────────────────────────────
        // 2. REFERENCED SONGS = EVERYTHING LEFT OVER
        // ──────────────────────────────────────────────

        // Get ALL songs on the site
        $all_song_ids = get_posts([
            'post_type'      => 'song',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]);

        // Combine all the lists we already accounted for
        $accounted_ids = array_merge($thread_ids, $episode_ids, $strand_ids, $featured_ids);
        
        // Whatever is left over becomes "Referenced"
        $referenced_ids = array_diff($all_song_ids, array_unique($accounted_ids));


        // ──────────────────────────────────────────────
        // 3. OPTIONAL: EXCLUDE RAP (Matching your original code)
        // ──────────────────────────────────────────────
        // Your original code excluded Rap from all tiers. 
        // If you want to remove this exclusion, just delete this entire block.
        
        $rap_songs = get_posts([
            'post_type'      => 'song',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => [
                ['taxonomy' => 'song_category', 'field' => 'slug', 'terms' => 'rap']
            ]
        ]);

        if (!empty($rap_songs)) {
            $thread_ids     = array_diff($thread_ids, $rap_songs);
            $episode_ids    = array_diff($episode_ids, $rap_songs);
            $strand_ids     = array_diff($strand_ids, $rap_songs);
            $featured_ids   = array_diff($featured_ids, $rap_songs);
            $referenced_ids = array_diff($referenced_ids, $rap_songs);
        }


        // ──────────────────────────────────────────────
        // 4. RENDER THE SECTIONS
        // ──────────────────────────────────────────────

        function lr_render_song_section($title, $song_ids) {
            if (empty($song_ids)) return;

            $songs = new WP_Query([
                'post_type'      => 'song',
                'posts_per_page' => -1,
                'post__in'       => $song_ids,
                'orderby'        => 'title',
                'order'          => 'ASC',
            ]);

            if (!$songs->have_posts()) return;
            ?>
            <div class="cpt-song-tier-group">
                <h3 class="cpt-song-tier-title"><?php echo esc_html($title); ?></h3>
                <div class="cpt-song-grid">
                    <?php while ($songs->have_posts()): $songs->the_post();
                        $cover   = get_field('cover_image');
                        $img_url = $cover ? $cover['sizes']['thumbnail'] : '';
                    ?>
                        <article class="cpt-song-grid-item">
                            <a href="<?php the_permalink(); ?>" class="cpt-song-grid-link">
                                <?php if ($img_url): ?>
                                    <img src="<?php echo esc_url($img_url); ?>"
                                         alt="<?php the_title_attribute(); ?>"
                                         class="cpt-song-grid-image">
                                <?php endif; ?>
                                <h3 class="cpt-song-grid-title"><?php the_title(); ?></h3>
                            </a>
                        </article>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php wp_reset_postdata();
        }

        // Render in order
        lr_render_song_section('📖 Narrative Thread Songs', $thread_ids);
        lr_render_song_section('🎬 Narrative Episode Songs', $episode_ids);
        lr_render_song_section('🧵 Narrative Strand Songs', $strand_ids);
        lr_render_song_section('🎧 Featured Songs', $featured_ids);
        lr_render_song_section('🎤 Referenced Songs', $referenced_ids);
        ?>

    </section>
</main>

<?php get_footer(); ?>