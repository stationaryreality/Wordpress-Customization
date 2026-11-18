<?php
/* Template Name: Artists Featured */
get_header();
?>

<main id="primary" class="site-main page-artists-featured">

    <?php
    $tiers = kp_get_artist_tiers();

    // Render function for artist sections
    if (!function_exists('lr_render_artist_section')) {
        function lr_render_artist_section($title, $artist_ids) {
            if (empty($artist_ids)) return;

            $artist_query = new WP_Query([
                'post_type'      => 'artist', // naturally excludes rappers now
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
                    get_template_part('template-parts/grids/artist', null, [
                        'artist_query' => $artist_query,
                        'title'        => '',
                        'emoji'        => '',
                    ]);
                    ?>
                </div>
                <?php
                wp_reset_postdata();
            }
        }
    }

    lr_render_artist_section('📖 Narrative Thread Artists', $tiers['thread']);
    lr_render_artist_section('🎬 Narrative Episode Artists', $tiers['episode']);
    lr_render_artist_section('🧵 Narrative Strand Artists', $tiers['strand']);
    lr_render_artist_section('🎧 Featured Artists', $tiers['featured']);
    lr_render_artist_section('🎤 Referenced Artists', $tiers['referenced']);
    ?>

</main>

<?php get_footer(); ?>