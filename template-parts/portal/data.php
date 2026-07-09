<?php

/*
|--------------------------------------------------------------------------
| PORTAL TERMS
|--------------------------------------------------------------------------
*/


$portal_terms = [];
$taxonomies   = get_object_taxonomies(get_post_type());

foreach ($taxonomies as $taxonomy) {

    $terms = wp_get_post_terms(
        get_the_ID(),
        $taxonomy,
        ['fields' => 'slugs']
    );

    if (!empty($terms) && !is_wp_error($terms)) {
        $portal_terms[$taxonomy] = $terms;
    }
}

if (empty($portal_terms)) {

    $portal_data = [
        'error' => 'No taxonomy relationships found.'
    ];

    return;
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

/*
|--------------------------------------------------------------------------
| SECTION ORDER
|--------------------------------------------------------------------------
*/

$section_order = [
    'concept',
    'quote',
    'song',
    'book',
    'movie',
    'excerpt',
    'lyric',
    'image',
    'element',
];

/*
|--------------------------------------------------------------------------
| TAX QUERY
|--------------------------------------------------------------------------
*/

$tax_query = ['relation' => 'OR'];

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

$args = [
    'post_type'      => $post_types,
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'title',
    'order'          => 'ASC',
    'tax_query'      => $tax_query,
];

$query = new WP_Query($args);

/*
|--------------------------------------------------------------------------
| SECTION STORAGE
|--------------------------------------------------------------------------
*/

$sections = [];
$total_entries = 0;

foreach ($section_order as $type) {
    $sections[$type] = [];
}

/*
|--------------------------------------------------------------------------
| BUILD DATA
|--------------------------------------------------------------------------
*/

if ($query->have_posts()) :

    while ($query->have_posts()) :

        $query->the_post();

        $post_id = get_the_ID();
        $type    = get_post_type();

        if ($type === 'portal') {
            continue;
        }




/*
|--------------------------------------------------------------------------
| STORE
|--------------------------------------------------------------------------
*/

$sections[$type][] = kp_build_card(
    $type,
    $post_id,
    $map
);

$total_entries++;


    endwhile;

    wp_reset_postdata();

endif;

/*
|--------------------------------------------------------------------------
| ACTIVE SECTIONS
|--------------------------------------------------------------------------
*/

$active_sections = [];

foreach ($sections as $type => $entries) {

    if (!empty($entries)) {
        $active_sections[$type] = count($entries);
    }
}


$portal_data = kp_build_knowledge(

    $sections,

    [

        'section_order'  => $section_order,

        'section_labels' => $section_labels,

        'map'            => $map,

        'context' => [

            'type' => 'portal',

            'post_id' => get_the_ID(),

        ],

    ]

);
