<?php

$meta = get_field('author');
$cover = get_field('cover_image');
$image = $cover ? $cover['sizes']['medium'] : '';

return compact(
    'title',
    'url',
    'icon',
    'excerpt',
    'image',
    'meta',
    'type'
);