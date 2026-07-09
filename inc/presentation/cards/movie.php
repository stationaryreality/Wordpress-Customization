<?php

$cover = get_field('cover_image');
$image = $cover
    ? $cover['sizes']['medium']
    : get_the_post_thumbnail_url($post_id, 'medium');

return compact(
    'title',
    'url',
    'icon',
    'excerpt',
    'image',
    'meta',
    'type'
);