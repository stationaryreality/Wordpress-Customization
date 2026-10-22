<?php
/* Template Name: Songs Featured */
get_header();
?>
<main id="primary" class="site-main cpt-song-archive">
    <section class="cpt-song-section">

        <?php
        // ══════════════════════════════════════════════
        // THIS IS THE FIX: We ONLY pull from the exclusive helper.
        // Because of how the helper works, $tiers['featured'] 
        // CANNOT contain any song that is already in a strand.
        // ══════════════════════════════════════════════
        $tiers = kp_get_song_tiers(); 
        
        $thread_ids     = $tiers['thread'];
        $episode_ids    = $tiers['episode'];
        $strand_ids     = $tiers['strand'];
        $featured_ids   = $tiers['featured']; 
        $referenced_ids = $tiers['referenced'];

        // Optional: exclude Rap (preserved from your original)
        $rap_songs = get_posts([
            'post_type'=>'song','posts_per_page'=>-1,'fields'=>'ids',
            'tax_query'=>[['taxonomy'=>'song_category','field'=>'slug','terms'=>'rap']]
        ]);
        if (!empty($rap_songs)) {
            $thread_ids     = array_values(array_diff($thread_ids, $rap_songs));
            $episode_ids    = array_values(array_diff($episode_ids, $rap_songs));
            $strand_ids     = array_values(array_diff($strand_ids, $rap_songs));
            $featured_ids   = array_values(array_diff($featured_ids, $rap_songs));
            $referenced_ids = array_values(array_diff($referenced_ids, $rap_songs));
        }

        // Render function
        if (!function_exists('lr_render_song_section')) {
            function lr_render_song_section($title, $song_ids) {
                if (empty($song_ids)) return;
                $songs = new WP_Query([
                    'post_type'=>'song','posts_per_page'=>-1,
                    'post__in'=>$song_ids,'orderby'=>'title','order'=>'ASC',
                ]);
                if (!$songs->have_posts()) return;
                ?>
                <div class="cpt-song-tier-group">
                    <h3 class="cpt-song-tier-title"><?php echo esc_html($title); ?></h3>
                    <div class="cpt-song-grid">
                        <?php while ($songs->have_posts()): $songs->the_post();
                            $cover = get_field('cover_image');
                            $img_url = $cover ? $cover['sizes']['thumbnail'] : '';
                        ?>
                            <article class="cpt-song-grid-item">
                                <a href="<?php the_permalink(); ?>" class="cpt-song-grid-link">
                                    <?php if ($img_url): ?>
                                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title_attribute(); ?>" class="cpt-song-grid-image">
                                    <?php endif; ?>
                                    <h3 class="cpt-song-grid-title"><?php the_title(); ?></h3>
                                </a>
                            </article>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php wp_reset_postdata();
            }
        }

        lr_render_song_section('📖 Narrative Thread Songs', $thread_ids);
        lr_render_song_section('🎬 Narrative Episode Songs', $episode_ids);
        lr_render_song_section('🧵 Narrative Strand Songs', $strand_ids);
        lr_render_song_section('🎧 Featured Songs', $featured_ids);
        lr_render_song_section('🎤 Referenced Songs', $referenced_ids);
        ?>

    </section>
</main>
<?php get_footer(); ?>