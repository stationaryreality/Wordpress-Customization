<?php

/**
 * Render a knowledge view.
 *
 * $view:
 *   index
 *   list
 *   atlas
 *
 * $data:
 *   Whatever the selected view expects.
 */

function kp_render_knowledge_view($view, array $data = [])
{
    $allowed = [
        'index',
        'list',
        'atlas',
    ];

    if (!in_array($view, $allowed, true)) {
        $view = 'index';
    }

    get_template_part(
        'template-parts/presentation/views/' . $view,
        null,
        $data
    );
}