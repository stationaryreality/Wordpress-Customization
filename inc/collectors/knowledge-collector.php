<?php

/**
 * Get all taxonomy terms attached to a post.
 *
 * Returns:
 *
 * [
 *     'topic' => ['philosophy', 'science'],
 *     'theme' => ['education'],
 * ]
 */
function kp_get_object_terms(int $post_id): array
{
    $portal_terms = [];

    $taxonomies = get_object_taxonomies(get_post_type($post_id));

    foreach ($taxonomies as $taxonomy) {

        $terms = wp_get_post_terms(
            $post_id,
            $taxonomy,
            [
                'fields' => 'slugs',
            ]
        );

        if (!empty($terms) && !is_wp_error($terms)) {
            $portal_terms[$taxonomy] = $terms;
        }
    }

    return $portal_terms;
}


//Collector

function kp_collect_knowledge(array $args = [])
{
    $post_id = $args['portal'] ?? 0;

    if (!$post_id) {
        return [
            'error' => 'No portal specified.',
        ];
    }

    $portal_terms = kp_get_object_terms($post_id);

    if (empty($portal_terms)) {

        return [
            'error' => 'No taxonomy relationships found.',
        ];

    }

    /*
    |--------------------------------------------------------------------------
    | CPT METADATA
    |--------------------------------------------------------------------------
    */

    $map = get_cpt_metadata();

    /*
    |--------------------------------------------------------------------------
    | INCLUDED CPTS
    |--------------------------------------------------------------------------
    */

    $post_types = [
        'concept',
        'quote',
        'song',
        'book',
        'movie',
        'excerpt',
        'lyric',
        'image',
        'element',
        'artist',
        'chapter',
        'fragment',
        'gamne',
        'organization',
        'portal',
        'profile',
        'show',
    
    ];

    /*
    |--------------------------------------------------------------------------
    | SECTION LABELS
    |--------------------------------------------------------------------------
    */

    $section_labels = [

        'concept' => 'Concepts',
        'quote'   => 'Quotes',
        'song'    => 'Songs',
        'book'    => 'Books',
        'movie'   => 'Movies',
        'excerpt' => 'Excerpts',
        'lyric'   => 'Lyrics',
        'image'   => 'Images',
        'element' => 'Elements',

    ];

    $section_order = $post_types;

    /*
    |--------------------------------------------------------------------------
    | TAX QUERY
    |--------------------------------------------------------------------------
    */

    $tax_query = [
        'relation' => 'OR',
    ];

    foreach ($portal_terms as $taxonomy => $slugs) {

        $tax_query[] = [

            'taxonomy' => $taxonomy,
            'field'    => 'slug',
            'terms'    => $slugs,

        ];

    }

    /*
    |--------------------------------------------------------------------------
    | QUERY
    |--------------------------------------------------------------------------
    */

    $query = new WP_Query([

        'post_type'      => $post_types,
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
        'tax_query'      => $tax_query,

    ]);

    /*
    |--------------------------------------------------------------------------
    | STORAGE
    |--------------------------------------------------------------------------
    */

    $sections = [];

    foreach ($section_order as $type) {
        $sections[$type] = [];
    }

    while ($query->have_posts()) {

        $query->the_post();

        $type = get_post_type();

        if ($type === 'portal') {
            continue;
        }

        $sections[$type][] = kp_build_card(
            $type,
            get_the_ID(),
            $map
        );

    }

    wp_reset_postdata();

    return kp_build_knowledge(

        $sections,

        [

            'section_order'  => $section_order,
            'section_labels' => $section_labels,
            'map'            => $map,

            'context' => [

                'type'    => 'portal',
                'post_id' => $post_id,

            ],

        ]

    );

}