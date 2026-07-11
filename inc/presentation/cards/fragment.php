<?php

$image = has_post_thumbnail($post_id)
    ? get_the_post_thumbnail_url($post_id, 'medium')
    : '';

$excerpt = get_the_excerpt($post_id);

return compact(
    'title',
    'url',
    'icon',
    'excerpt',
    'image',
    'meta',
    'type'
);