<?php
/**
 * Taxonomy Context Collector
 *
 * Builds normalized topic/theme context for a knowledge object.
 *
 * Relationship traversal is delegated to kp_build_reference_context().
 * This collector is responsible only for aggregating taxonomy terms.
 */

function kp_build_taxonomy_context($post_id) {

    $context = [
        'topic' => [],
        'theme' => [],
    ];

    $post_id = (int) $post_id;

    if (!$post_id) {
        return $context;
    }

    /*
     * Include terms directly attached to the starting object.
     */
    foreach (['topic', 'theme'] as $taxonomy) {

        $terms = get_the_terms($post_id, $taxonomy);

        if (!$terms || is_wp_error($terms)) {
            continue;
        }

        foreach ($terms as $term) {
            $context[$taxonomy][$term->term_id] = $term;
        }
    }

    /*
     * Use the existing relationship collector to discover
     * related CPTs, including CPTs reached through Elements.
     */
    $reference_context = kp_build_reference_context($post_id);

    foreach ($reference_context as $items) {

        if (!is_array($items)) {
            continue;
        }

        foreach ($items as $item) {

            if (!$item || empty($item->ID)) {
                continue;
            }

            foreach (['topic', 'theme'] as $taxonomy) {

                $terms = get_the_terms($item->ID, $taxonomy);

                if (!$terms || is_wp_error($terms)) {
                    continue;
                }

                foreach ($terms as $term) {
                    $context[$taxonomy][$term->term_id] = $term;
                }
            }
        }
    }

    /*
     * Convert associative term maps into sorted arrays.
     */
    foreach (['topic', 'theme'] as $taxonomy) {

        $context[$taxonomy] = array_values($context[$taxonomy]);

        usort(
            $context[$taxonomy],
            function ($a, $b) {
                return strcmp($a->name, $b->name);
            }
        );
    }

    return $context;
}