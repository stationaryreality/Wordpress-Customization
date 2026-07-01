<?php
/**
 * Shared relationship traversal helper.
 *
 * Centralizes Element expansion and relationship insertion so every
 * collector applies identical traversal and deduplication rules.
 *
 * Keeping this logic in one location prevents collectors from
 * gradually diverging over time.
 */

function kp_add_related_items_to_context(&$context, $related) {

    if (!$related) {
        return;
    }

    foreach ($related as $item) {

        $item_type = get_post_type($item);

        // Never include narrative containers
        if (in_array($item_type, ['chapter', 'fragment', 'element'])) {
            continue;
        }

        $context[$item_type][$item->ID] = $item;
    }
}

/**
 * Builds the complete relationship context for a narrative object.
 *
 * This collector acts as the primary entry point for discovering
 * all CPT relationships belonging to a Chapter, Fragment, or Element.
 *
 * Renderers should consume this context instead of querying ACF
 * directly, allowing traversal rules to remain centralized.
 */

function kp_build_reference_context($post_id) {

    $context = [];
    $type = get_post_type($post_id);

// --------------------------
// Elements are different.
// They store everything in one related_content field.
// --------------------------

if ($type === 'element') {

$related = get_field('related_content', $post_id);

kp_add_related_items_to_context($context, $related);

return $context;

}

// --------------------------
// Chapter / Fragment fields
// --------------------------

$field_map = [
    'quote'        => 'quotes_referenced',
    'excerpt'      => 'excerpts_referenced',
    'image'        => 'images_linked',
    'lyric'        => 'lyrics_referenced',
    'book'         => 'books_cited',
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

foreach ($field_map as $item_type => $field) {

    $items = get_field($field, $post_id);

    if (!$items) {
        continue;
    }

    foreach ($items as $item) {
        $context[$item_type][$item->ID] = $item;
    }
}

// --------------------------
// Expand attached Elements
// --------------------------

$elements = get_field('attached_elements', $post_id);

if ($elements) {

    foreach ($elements as $element) {

    $related = get_field('related_content', $element->ID);

    kp_add_related_items_to_context($context, $related);
    }
}

return $context;

}