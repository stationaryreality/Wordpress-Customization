<?php
/**
 * Build complete footnote context.
 *
 * Expands attached Elements into their contained CPTs while
 * preventing Chapter/Fragment recursion.
 */
function kp_build_reference_context($post_id) {

    $context = [];

    // --------------------------
    // Direct Chapter/Fragment fields
    // --------------------------

    $field_map = [
        'quote'        => 'quotes_referenced',
        'excerpt'      => 'excerpts_referenced',
        'image'        => 'images_linked',
        'lyric'        => 'lyrics_referenced',
        'book'         => 'books_referenced',
        'movie'        => 'movies_referenced',
        'show'         => 'shows_referenced',
        'game'         => 'games_referenced',
        'organization' => 'organizations_referenced',
        'concept'      => 'concepts_referenced',
        'person'       => 'people_referenced',
        'video'        => 'videos_referenced',
        'profile'      => 'profiles_referenced',
        'artist'       => 'artists_referenced',
    ];

    foreach ($field_map as $type => $field) {

        $items = get_field($field, $post_id);

        if (!$items) {
            continue;
        }

        foreach ($items as $item) {
            $context[$type][$item->ID] = $item;
        }
    }

    // --------------------------
    // Attached Elements
    // --------------------------

    $elements = get_field('attached_elements', $post_id);

    if ($elements) {

        foreach ($elements as $element) {

            $related = get_field('related_content', $element->ID);

            if (!$related) {
                continue;
            }

            foreach ($related as $item) {

                $type = get_post_type($item);

                // Never recurse into narrative containers
                if (in_array($type, ['chapter', 'fragment', 'element'])) {
                    continue;
                }

                $context[$type][$item->ID] = $item;
            }
        }
    }

    return $context;
}