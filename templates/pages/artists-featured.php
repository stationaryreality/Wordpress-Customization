<?php
/* Template Name: Artists Featured */
get_header();
?>

<main id="primary" class="site-main page-artists-featured">

    <?php
    // Pull exclusive artist tiers from the helper
    $tiers = kp_get_artist_tiers();
    
    $thread_artist_ids     = $tiers['thread'];
    $episode_artist_ids    = $tiers['episode'];
    $strand_artist_ids     = $tiers['strand'];
    $featured_artist_ids   = $tiers['featured'];
    $referenced_artist_ids = $tiers['referenced'];

    // Exclude rappers from all tier sections (they have their own grouping)
    $rappers = get_posts([
        'post_type'      => 'artist',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => [['taxonomy' => 'artist_type', 'field' => 'slug', 'terms' => 'rapper']]
    ]);

    if (!empty($rappers)) {
        $thread_artist_ids     = array_values(array_diff($thread_artist_ids, $rappers));
        $episode_artist_ids    = array_values(array_diff($episode_artist_ids, $rappers));
        $strand_artist_ids     = array_values(array_diff($strand_artist_ids, $rappers));
        $featured_artist_ids   = array_values(array_diff($featured_artist_ids, $rappers));
        $referenced_artist_ids = array_values(array_diff($referenced_artist_ids, $rappers));
    }

    // Render function for artist sections
    if (!function_exists('lr_render_artist_section')) {
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
                    set_query_var('artist_query', $artist_query);
                    get_template_part('template-parts/grids/artist');
                    ?>
                </div>
                <?php
                wp_reset_postdata();
            }
        }
    }

    // Render sections in priority order
    lr_render_artist_section('📖 Narrative Thread Artists', $thread_artist_ids);
    lr_render_artist_section('🎬 Narrative Episode Artists', $episode_artist_ids);
    lr_render_artist_section('🧵 Narrative Strand Artists', $strand_artist_ids);
    lr_render_artist_section('🎧 Featured Artists', $featured_artist_ids);
    lr_render_artist_section('🎤 Referenced Artists', $referenced_artist_ids);
    ?>

</main>

<?php get_footer(); ?>