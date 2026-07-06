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

        $type = $section['type'];

        $template = locate_template(
            "template-parts/search/{$type}.php"
        );

        if ($template) {

            get_template_part(
                "template-parts/search/{$type}",
                null,
                $section
            );

        } else {

            get_template_part(
                "template-parts/search/default",
                null,
                $section
            );

        }

    }

}