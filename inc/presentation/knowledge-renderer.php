<?php

/**
 * ==========================================================
 * Knowledge Renderer
 * ==========================================================
 */

/**
 * Render a Topic or Theme using the shared
 * Knowledge Sections presentation.
 */

function kp_render_taxonomy_knowledge($term) {

    if (!$term || is_wp_error($term)) {
        return;
    }

    $cpt_sections = get_cpt_metadata();

    kp_render_knowledge_sections(
        $term,
        $cpt_sections
    );

}