<?php
/**
 * Render a collection of normalized knowledge sections.
 *
 * Each section should contain:
 *  - type
 *  - query
 *  - info
 *  - search_term
 *  - term (optional)
 */
function kp_render_knowledge_sections(array $sections) {

    foreach ($sections as $section) {

        $type  = $section['type'] ?? '';
        $query = $section['query'] ?? null;

        if (!$type) {
            continue;
        }

        if (!$query instanceof WP_Query) {
            continue;
        }

        if (!$query->have_posts()) {
            continue;
        }

        $template = locate_template(
            "template-parts/search/{$type}.php"
        );

        if (!$template) {

            continue;
        }

        get_template_part(
            "template-parts/search/{$type}",
            null,
            $section
        );
    }
}