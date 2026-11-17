<?php
/**
 * Top Content: section discovery, query, and card rendering.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Discover all enabled section config files.
 * Sections are plain PHP files that return an array.
 */
function top_content_get_sections() {
    static $sections = null;

    if ( null !== $sections ) {
        return $sections;
    }

    $sections = [];
    $dir      = get_stylesheet_directory() . '/inc/top-content/sections/';
    $files    = glob( $dir . '*.php' );

    if ( $files ) {
        foreach ( $files as $file ) {
            $section = include $file;

            if ( ! is_array( $section ) || empty( $section['key'] ) || empty( $section['post_type'] ) ) {
                continue;
            }

            $section = wp_parse_args( $section, [
                'enabled'     => true,
                'priority'    => 100,
                'title'       => ucfirst( $section['key'] ),
                'emoji'       => '',
                'theme_slug'  => '',
                'layout'      => 'grid',
                'full_page'   => '',
                'description' => '',
                'query_args'  => [],
            ] );

            if ( empty( $section['enabled'] ) ) {
                continue;
            }

            // Validate layout against known layouts (filterable for future ones).
            $allowed = apply_filters( 'top_content_layouts', [ 'grid', 'list', 'image-grid' ] );
            if ( ! in_array( $section['layout'], $allowed, true ) ) {
                $section['layout'] = 'grid';
            }

            $sections[ $section['key'] ] = $section;
        }
    }

    uasort( $sections, function ( $a, $b ) {
        return ( $a['priority'] <=> $b['priority'] ) ?: strcmp( $a['title'], $b['title'] );
    } );

    $sections = apply_filters( 'top_content_sections', $sections );

    return $sections;
}

/**
 * Resolve the active section key from ?view=, with safe fallback.
 */
function top_content_get_current_section_key( $sections ) {
    $requested = isset( $_GET['view'] ) ? sanitize_key( wp_unslash( $_GET['view'] ) ) : '';

    if ( $requested && isset( $sections[ $requested ] ) ) {
        return $requested;
    }

    if ( isset( $sections['threads'] ) ) {
        return 'threads';
    }

    $keys = array_keys( $sections );
    return $keys ? $keys[0] : '';
}

/**
 * Build the query for a section.
 */
function top_content_get_query( $config ) {
    $args = [
        'post_type'      => $config['post_type'],
        'post_status'    => 'publish',
        'posts_per_page' => 100,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ];

    if ( ! empty( $config['theme_slug'] ) ) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'theme',
                'field'    => 'slug',
                'terms'    => $config['theme_slug'],
            ],
        ];
    }

    if ( ! empty( $config['query_args'] ) && is_array( $config['query_args'] ) ) {
        $args = array_merge( $args, $config['query_args'] );
    }

    return new WP_Query( apply_filters( 'top_content_query_args', $args, $config ) );
}

/**
 * Render one card inside the loop.
 *
 * Fallback order (first found wins):
 *   cards/{layout}-{section_key}.php
 *   cards/{layout}-{post_type}.php
 *   cards/{layout}.php
 *
 * This is the escape hatch: a CPT only needs its own card file
 * when its markup genuinely differs.
 */
function top_content_render_card( $config ) {
    $candidates = [
        sprintf( 'template-parts/top/cards/%s-%s.php', $config['layout'], $config['key'] ),
        sprintf( 'template-parts/top/cards/%s-%s.php', $config['layout'], $config['post_type'] ),
        sprintf( 'template-parts/top/cards/%s.php', $config['layout'] ),
    ];

    $template = locate_template( $candidates );

    if ( $template ) {
        include $template; // $config is in scope for the card file.
    }
}

/**
 * Active layout key — exposed so the conditional CSS loader
 * can enqueue only the layout CSS it needs.
 */
function top_content_get_active_layout() {
    $sections = top_content_get_sections();
    $key      = top_content_get_current_section_key( $sections );

    return isset( $sections[ $key ] ) ? $sections[ $key ]['layout'] : '';
}