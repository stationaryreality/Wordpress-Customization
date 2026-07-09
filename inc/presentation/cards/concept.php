<?php

$excerpt = get_field('definition');

$image = has_post_thumbnail($post_id)
    ? get_the_post_thumbnail_url($post_id, 'medium')
    : '';

return compact(
    'title',
    'url',
    'icon',
    'excerpt',
    'image',
    'meta',
    'type'
);