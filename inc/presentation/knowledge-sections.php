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
function kp_render_knowledge_sections(array $knowledge)
{
    foreach ($knowledge['sections'] as $type => $cards) {

        if (empty($cards)) {
            continue;
        }

        get_template_part(
            "template-parts/search/{$type}",
            null,
            [
                'items' => $cards,
                'type'  => $type,
            ]
        );
    }
}