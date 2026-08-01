<?php
/* Template Name: Songs Featured */
get_header();
?>

<main id="primary" class="site-main cpt-song-archive">

    <section class="cpt-song-section">
        <?php
        $song_tiers = [
            'narrative'  => '📖 Narrative Thread Songs',
            'featured'   => '🎧 Featured Songs',
            'referenced' => '🎤 Referenced Songs',
        ];

        foreach ($song_tiers as $slug => $label):
            $songs = new WP_Query([
                'post_type'      => 'song',
                'posts_per_page' => -1,
                'tax_query'      => [
                    [
                        'taxonomy' => 'feature_level',
                        'field'    => 'slug',
                        'terms'    => $slug,
                    ],
                    [
                        'taxonomy' => 'song_category',
                        'field'    => 'slug',
                        'terms'    => ['rap'],
                        'operator' => 'NOT IN',
                    ],
                ],
                'orderby' => 'title',
                'order'   => 'ASC',
            ]);

            if ($songs->have_posts()): ?>
                <div class="cpt-song-tier-group">
                    <h3 class="cpt-song-tier-title"><?php echo esc_html($label); ?></h3>

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
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </section>

</main>

<?php get_footer(); ?>