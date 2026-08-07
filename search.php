<?php

get_header();

$search_term = get_search_query();

$cpt_sections = get_cpt_metadata();

echo '<main class="search-results">';
echo '<h1>Search results for “' . esc_html($search_term) . '”</h1>';

/*
|--------------------------------------------------------------------------
| BUILD TAXONOMY MATCHES
|--------------------------------------------------------------------------
|
| Search currently includes posts belonging to an exact matching
| Topic or Theme in addition to normal Relevanssi results.
|
*/

$taxonomies_to_include = [
    'theme',
    'topic',
];

$term_posts_by_type = [];

$search_term_lower = mb_strtolower($search_term);
$search_term_slug  = sanitize_title($search_term);

foreach ($taxonomies_to_include as $taxonomy) {

    $terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        continue;
    }

    foreach ($terms as $term) {

        $name_matches = (
            mb_strtolower($term->name) === $search_term_lower
        );

        $slug_matches = (
            $term->slug === $search_term_slug
        );

        if (!$name_matches && !$slug_matches) {
            continue;
        }

        $posts_in_term = get_posts([
            'post_type'      => array_keys($cpt_sections),
            'tax_query'      => [
                [
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $term->term_id,
                ],
            ],
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]);

        foreach ($posts_in_term as $post_id) {

            $post_type = get_post_type($post_id);

            if (!$post_type) {
                continue;
            }

            $term_posts_by_type[$post_type][] = (int) $post_id;
        }
    }
}

/*
|--------------------------------------------------------------------------
| NORMALIZE TAXONOMY POST IDS
|--------------------------------------------------------------------------
*/

foreach ($term_posts_by_type as $post_type => $ids) {

    $term_posts_by_type[$post_type] = array_values(
        array_unique($ids)
    );
}

/*
|--------------------------------------------------------------------------
| BUILD SEARCH SECTIONS
|--------------------------------------------------------------------------
|
| Each CPT becomes a normalized knowledge section.
|
*/

$sections = [];

/*
|--------------------------------------------------------------------------
| PORTALS FIRST
|--------------------------------------------------------------------------
*/

if (isset($cpt_sections['portal'])) {

    $sections[] = kp_build_search_section(
        'portal',
        $cpt_sections['portal'],
        $search_term,
        $term_posts_by_type
    );
}

/*
|--------------------------------------------------------------------------
| REMAINING CPTS
|--------------------------------------------------------------------------
*/

foreach ($cpt_sections as $type => $info) {

    if ($type === 'portal') {
        continue;
    }

    $sections[] = kp_build_search_section(
        $type,
        $info,
        $search_term,
        $term_posts_by_type
    );
}

/*
|--------------------------------------------------------------------------
| TAXONOMY RESULTS
|--------------------------------------------------------------------------
|
| Topics and Themes remain separate from CPT results for now.
|
| This is intentional. The eventual architecture can normalize
| taxonomy results into their own context/section layer so Search
| and Taxonomy views can share the same presentation system.
|
*/

render_taxonomy_results(
    'topic',
    'Topics',
    '🧩',
    $search_term
);

render_taxonomy_results(
    'theme',
    'Themes',
    '🎨',
    $search_term
);

/*
|--------------------------------------------------------------------------
| RENDER CPT SEARCH RESULTS
|--------------------------------------------------------------------------
*/

kp_render_knowledge_sections($sections);

echo '</main>';

get_footer();