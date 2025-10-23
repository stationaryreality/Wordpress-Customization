<?php
/**
 * Concept Related Concepts Bidirectional Sync
 * Automatically keeps ACF relationship field "related_concepts" in sync both directions.
 */

add_action('acf/save_post', function($post_id) {

    // Only run for Concept CPT
    if (get_post_type($post_id) !== 'concept') {
        return;
    }

    $field_name = 'related_concepts';
    $field_value = get_field($field_name, $post_id);

    if (empty($field_value)) {
        $field_value = [];
    }

    // Get old value (before save)
    $old_value = get_field($field_name, $post_id, false);
    if (empty($old_value)) {
        $old_value = [];
    }

    // Remove reverse links from concepts no longer related
    foreach ($old_value as $old_id) {
        if (!in_array($old_id, $field_value)) {
            $others = get_field($field_name, $old_id, false);
            if (!empty($others) && in_array($post_id, $others)) {
                $others = array_diff($others, [$post_id]);
                update_field($field_name, array_values($others), $old_id);
            }
        }
    }

    // Add reverse links for new relationships
    foreach ($field_value as $related_id) {
        $others = get_field($field_name, $related_id, false);
        if (empty($others)) {
            $others = [];
        }
        if (!in_array($post_id, $others)) {
            $others[] = $post_id;
            update_field($field_name, $others, $related_id);
        }
    }

}, 20);
