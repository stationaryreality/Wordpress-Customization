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

    extract($data);

    include locate_template(
        "inc/presentation/views/{$view}.php"
    );
}