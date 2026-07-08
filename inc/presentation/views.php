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

function kp_load_knowledge_view($view, array $data = [])
{
    $allowed = [
        'index',
        'list',
        'atlas',
    ];

    if (!in_array($view, $allowed, true)) {
        $view = 'index';
    }

    /*
     * Make every data variable available
     * exactly like the old portal include.
     */

    extract($data, EXTR_SKIP);

    include locate_template(
        "inc/presentation/views/{$view}.php"
    );
}