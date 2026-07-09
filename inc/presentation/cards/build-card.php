<?php

function kp_build_card($type, $post_id, array $map = [])
{
    $title   = get_the_title($post_id);
    $url     = get_permalink($post_id);
    $icon    = $map[$type]['emoji'] ?? '✦';
    $excerpt = '';
    $image   = '';
    $meta    = '';

    $card = locate_template(
        "inc/presentation/cards/{$type}.php"
    );

    if ($card) {

        return include $card;

    }

    return compact(
        'title',
        'url',
        'icon',
        'excerpt',
        'image',
        'meta',
        'type'
    );
}