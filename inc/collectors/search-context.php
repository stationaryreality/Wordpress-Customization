<?php

/**
 * Build normalized Search context.
 *
 * Search remains responsible for finding matching content.
 * Presentation remains responsible for rendering it.
 *
 * Taxonomy results are intentionally NOT included here yet.
 * That bridge can be addressed separately once the CPT search
 * migration is stable.
 */


/**
 * Build a map:
 *
 * post_type => [term-matching post IDs]
 *
 * Matches taxonomy terms by exact name or slug.
 */
function kp_build_search_taxonomy_matches(
    array $cpt_sections,
    string $search_term,
    array $taxonomies = ['theme', 'topic']
): array {

    $term_posts_by_type = [];

    $search_term_lower = mb_strtolower($search_term);
    $search_term_slug  = sanitize_title($search_term);

    foreach ($taxonomies as $taxonomy) {

        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        ]);

        if (empty($terms) || is_wp_error($terms)) {
            continue;
        }

        foreach ($terms as $term) {

            if (
                mb_strtolower($term->name) !== $search_term_lower
                && $term->slug !== $search_term_slug
            ) {
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

    foreach ($term_posts_by_type as $post_type => $ids) {

        $term_posts_by_type[$post_type] = array_values(
            array_unique($ids)
        );
    }

    return $term_posts_by_type;
}


/**
 * Build a single CPT search section.
 *
 * Preserves the existing Search behavior:
 *
 * - WordPress search
 * - Relevanssi
 * - exact taxonomy matches
 * - merged results
 */
function kp_build_search_section(
    string $type,
    array $info,
    string $search_term,
    array $term_posts_by_type = []
): array {

    $query_args = [
        'post_type'      => $type,
        's'              => $search_term,
        'posts_per_page' => -1,
        'relevanssi'     => true,
    ];

    $query = new WP_Query($query_args);

    if (function_exists('relevanssi_do_query')) {
        relevanssi_do_query($query);
    }

    /*
     * Add posts found through an exact taxonomy match
     * when they weren't already returned by Relevanssi.
     */
    if (!empty($term_posts_by_type[$type])) {

        $existing_ids = [];

        foreach ((array) $query->posts as $post) {

            $existing_ids[] =
                is_object($post)
                    ? $post->ID
                    : intval($post);
        }

        $new_ids = array_diff(
            $term_posts_by_type[$type],
            $existing_ids
        );

        if (!empty($new_ids)) {

            $new_posts = get_posts([
                'post_type'      => $type,
                'post__in'       => $new_ids,
                'posts_per_page' => -1,
                'orderby'        => 'post__in',
            ]);

            $query->posts = array_merge(
                (array) $query->posts,
                $new_posts
            );

            $query->post_count = count($query->posts);

            $query->found_posts = max(
                $query->found_posts,
                $query->post_count
            );
        }
    }

    return [
        'type'        => $type,
        'query'       => $query,
        'info'        => $info,
        'search_term' => $search_term,
    ];
}


/**
 * Build the complete CPT Search context.
 *
 * Returns:
 *
 * [
 *     'search_term' => string,
 *     'sections'    => [
 *         [
 *             'type',
 *             'query',
 *             'info',
 *             'search_term'
 *         ]
 *     ]
 * ]
 *
 * Taxonomy presentation remains outside this context
 * for now and can be migrated separately.
 */
function kp_build_search_context(
    string $search_term,
    array $args = []
): array {

    $cpt_sections = $args['cpt_sections']
        ?? get_cpt_metadata();

    $term_posts_by_type =
        kp_build_search_taxonomy_matches(
            $cpt_sections,
            $search_term,
            $args['taxonomies'] ?? ['theme', 'topic']
        );

    $sections = [];

    /*
     * Portal remains first.
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
     * Remaining CPTs.
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

    return [
        'search_term'        => $search_term,
        'sections'           => $sections,
        'term_posts_by_type' => $term_posts_by_type,
    ];
}