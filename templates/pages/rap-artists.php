<?php
/* Template Name: Rap Artists */
get_header();
?>

<main id="primary" class="site-main page-rap-artists">

    <?php
    // Set to true when you want rappers grouped by narrative tiers.
    $use_rapper_tiers = false;

    if (!function_exists('lr_render_rapper_section')) {
        function lr_render_rapper_section($title, $rapper_ids) {
            if (empty($rapper_ids)) return;

            $rapper_query = new WP_Query([
                'post_type'      => 'rapper',
                'posts_per_page' => -1,
                'post__in'       => $rapper_ids,
                'orderby'        => 'title',
                'order'          => 'ASC',
            ]);

            if ($rapper_query->have_posts()) {
                ?>
                <div class="cpt-artist-feature-group">
                    <h3 class="cpt-artist-feature-level"><?php echo esc_html($title); ?></h3>
                    <?php
                    get_template_part('template-parts/grids/rapper', null, [
                        'query' => $rapper_query,
                        'title' => '',
                        'emoji' => '',
                    ]);
                    ?>
                </div>
                <?php
                wp_reset_postdata();
            }
        }
    }

    if ($use_rapper_tiers) {
        if (function_exists('kp_get_rapper_tiers')) {
            $tiers = kp_get_rapper_tiers();
            lr_render_rapper_section('📖 Narrative Thread Rappers', $tiers['thread'] ?? []);
            lr_render_rapper_section('🎬 Narrative Episode Rappers', $tiers['episode'] ?? []);
            lr_render_rapper_section('🧵 Narrative Strand Rappers', $tiers['strand'] ?? []);
            lr_render_rapper_section('🎧 Featured Rappers', $tiers['featured'] ?? []);
            lr_render_rapper_section('🎤 Referenced Rappers', $tiers['referenced'] ?? []);
        } else {
            echo '<p>Rapper tiers are on, but <code>kp_get_rapper_tiers()</code> is missing.</p>';
        }
    } else {
        $rapper_query = new WP_Query([
            'post_type'      => 'rapper',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);

        if ($rapper_query->have_posts()) {
            ?>
            <div class="cpt-artist-feature-group">
                <h3 class="cpt-artist-feature-level">🎤 All Rappers</h3>
                <?php
                get_template_part('template-parts/grids/rapper', null, [
                    'query' => $rapper_query,
                    'title' => '',
                    'emoji' => '',
                ]);
                ?>
            </div>
            <?php
            wp_reset_postdata();
        } else {
            echo '<p>No rappers found.</p>';
        }
    }
    ?>

</main>

<?php get_footer(); ?>