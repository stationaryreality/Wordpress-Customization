<?php

function kp_load_knowledge_view($view)
{
    $allowed = [
        'index',
        'list',
        'atlas',
    ];

    if (!in_array($view, $allowed, true)) {
        $view = 'index';
    }

    include locate_template(
        "inc/presentation/views/{$view}.php"
    );
}