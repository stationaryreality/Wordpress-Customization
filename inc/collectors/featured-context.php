<?php
/**
 * Build the Featured In context for a referenced CPT.
 *
 * Finds:
 * - Chapters that reference this post directly.
 * - Fragments that reference this post directly.
 * - Elements that reference this post.
 * - Chapters/Fragments that contain those Elements.
 *
 * Returns:
 *
 * [
 *     'chapters'  => [],
 *     'fragments' => [],
 *     'elements'  => [],
 * ]
 */
function kp_build_featured_context($meta_key, $post_id) {

    $context = [
        'chapters'  => [],
        'fragments' => [],
        'elements'  => [],
    ];

    // -----------------------------------------
    // Direct Chapters
    // -----------------------------------------

    $chapters = get_posts([
        'post_type'      => 'chapter',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'meta_query'     => [
            [
                'key'     => $meta_key,
                'value'   => '"' . $post_id . '"',
                'compare' => 'LIKE',
            ]
        ]
    ]);

    foreach ($chapters as $chapter) {
        $context['chapters'][$chapter->ID] = $chapter;
    }

    // -----------------------------------------
    // Direct Fragments
    // -----------------------------------------

    $fragments = get_posts([
        'post_type'      => 'fragment',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'meta_query'     => [
            [
                'key'     => $meta_key,
                'value'   => '"' . $post_id . '"',
                'compare' => 'LIKE',
            ]
        ]
    ]);

    foreach ($fragments as $fragment) {
        $context['fragments'][$fragment->ID] = $fragment;
    }

    // -----------------------------------------
    // Elements containing this CPT
    // -----------------------------------------

    $elements = get_posts([
        'post_type'      => 'element',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => [
            [
                'key'     => 'related_content',
                'value'   => '"' . $post_id . '"',
                'compare' => 'LIKE',
            ]
        ]
    ]);

    foreach ($elements as $element) {

        // Keep the Element itself
        $context['elements'][$element->ID] = $element;

        // -------------------------------------
        // Parent Chapters
        // -------------------------------------

        $parent_chapters = get_posts([
            'post_type'      => 'chapter',
            'posts_per_page' => -1,
            'meta_query'     => [
                [
                    'key'     => 'attached_elements',
                    'value'   => '"' . $element->ID . '"',
                    'compare' => 'LIKE',
                ]
            ]
        ]);

        foreach ($parent_chapters as $chapter) {
            $context['chapters'][$chapter->ID] = $chapter;
        }

        // -------------------------------------
        // Parent Fragments
        // -------------------------------------

        $parent_fragments = get_posts([
            'post_type'      => 'fragment',
            'posts_per_page' => -1,
            'meta_query'     => [
                [
                    'key'     => 'attached_elements',
                    'value'   => '"' . $element->ID . '"',
                    'compare' => 'LIKE',
                ]
            ]
        ]);

        foreach ($parent_fragments as $fragment) {
            $context['fragments'][$fragment->ID] = $fragment;
        }
    }

    return $context;
}