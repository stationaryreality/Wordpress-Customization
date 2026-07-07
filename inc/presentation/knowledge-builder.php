<?php

/**
 * Build normalized knowledge sections.
 */

function kp_build_knowledge_sections(array $resolved) {

    $sections = [];

    /*
    ---------------------------------------
    Determine taxonomy
    ---------------------------------------
    */

    switch ($resolved['type']) {

        case 'topic':

            $taxonomy = 'topic';
            $term = $resolved['term'];
            break;

        case 'theme':

            $taxonomy = 'theme';
            $term = $resolved['term'];
            break;

        default:

            return [];

    }

    /*
    ---------------------------------------
    CPTs
    ---------------------------------------
    */

    foreach (get_cpt_metadata() as $type => $info) {

        if (in_array($type, [
            'featured_artists',
            'other_artists',
            'songs_referenced',
        ])) {
            continue;
        }

        $query = new WP_Query([

            'post_type' => $type,

            'posts_per_page' => -1,

            'orderby' => 'title',

            'order' => 'ASC',

            'tax_query' => [[

                'taxonomy' => $taxonomy,
                'field' => 'term_id',
                'terms' => $term->term_id,

            ]]

        ]);

        if (!$query->have_posts()) {
            continue;
        }

        $sections[] = [

            'type' => $type,

            'query' => $query,

            'info' => $info,

            'search_term' => $term->name,

            'term' => $term,

        ];

    }

    return $sections;

}