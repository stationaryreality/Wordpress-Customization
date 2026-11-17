<?php
/* Template Name: Top Content */

get_header();

$sections = top_content_get_sections();
$current  = top_content_get_current_section_key( $sections );
$config   = isset( $sections[ $current ] ) ? $sections[ $current ] : null;
$query    = $config ? top_content_get_query( $config ) : null;
?>

<main class="top-content-page top-content-page--<?php echo esc_attr( $current ); ?>">

    <?php get_template_part( 'template-parts/top/hero' ); ?>

    <?php
    get_template_part( 'template-parts/top/nav', null, [
        'sections' => $sections,
        'current'  => $current,
    ] );
    ?>

    <?php if ( $config ) : ?>

        <section class="top-content-section top-content-section--<?php echo esc_attr( $config['key'] ); ?>">

            <?php
            get_template_part( 'template-parts/top/section-header', null, [
                'config' => $config,
            ] );
            ?>

            <?php if ( $query && $query->have_posts() ) : ?>

                <?php
                get_template_part(
                    'template-parts/top/layouts/' . $config['layout'],
                    null,
                    [
                        'query'  => $query,
                        'config' => $config,
                    ]
                );
                ?>

                <?php wp_reset_postdata(); ?>

            <?php else : ?>

                <?php
                get_template_part( 'template-parts/top/empty', null, [
                    'config' => $config,
                ] );
                ?>

            <?php endif; ?>

        </section>

        <?php
        get_template_part( 'template-parts/top/footer-links', null, [
            'config' => $config,
        ] );
        ?>

    <?php else : ?>

        <?php get_template_part( 'template-parts/top/empty' ); ?>

    <?php endif; ?>

</main>

<?php get_footer(); ?>