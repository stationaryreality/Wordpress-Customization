<?php

/**
 * Build normalized search context.
 *
 * This collector is intentionally limited to discovery.
 *
 * It does NOT:
 * - render anything
 * - load a presentation view
 * - call kp_render_knowledge_view()
 * - modify the main Search template
 *
 * Its job is simply to determine what knowledge objects and taxonomy
 * terms match a search term.
 */

function kp_build_search_context(string $search_term): array
{
    $search_term = trim($search_term);

    if ($search_term === '') {
        return [
            'search_term' => '',
            'sections'    => [],
            'taxonomy'    => [],
        ];
    }

    $cpt_sections = get_cpt_metadata();

    $sections = [];

    /*
     * ---------------------------------------------------------------
     * CPT SEARCH
     * ---------------------------------------------------------------
     */

    foreach ($cpt_sections as $type => $info) {

        $query = new WP_Query([
            'post_type'      => $type,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            's'              => $search_term,
            'relevanssi'     => true,
        ]);

        if (function_exists('relevanssi_do_query')) {
            relevanssi_do_query($query);
        }

        if (!$query->have_posts()) {
            continue;
        }

        $sections[$type] = [
            'type'        => $type,
            'query'       => $query,
            'info'        => $info,
            'search_term' => $search_term,
        ];
    }

    /*
     * ---------------------------------------------------------------
     * TAXONOMY SEARCH
     * ---------------------------------------------------------------
     *
     * Search currently treats Topics and Themes as searchable
     * knowledge alongside CPT results.
     *
     * Keep this data separate from CPT sections for now.
     */

    $taxonomy = [];

    foreach (['topic', 'theme'] as $taxonomy_name) {

        $terms = get_terms([
            'taxonomy'   => $taxonomy_name,
            'hide_empty' => false,
            'name__like' => $search_term,
        ]);

        if (is_wp_error($terms) || empty($terms)) {
            continue;
        }

        $taxonomy[$taxonomy_name] = [
            'taxonomy'    => $taxonomy_name,
            'terms'       => $terms,
            'search_term' => $search_term,
        ];
    }

    return [
        'search_term' => $search_term,
        'sections'    => $sections,
        'taxonomy'    => $taxonomy,
    ];
}