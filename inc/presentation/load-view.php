<?php

function kp_load_knowledge_view($view, array $data = [])
{
    $allowed = [
        'knowledge',
        'index',
        'list',
        'atlas',
    ];

    if (!in_array($view, $allowed, true)) {
        $view = 'index';
    }

    extract($data, EXTR_SKIP);

    include locate_template(
        "inc/presentation/views/{$view}.php"
    );
}