<?php

$cover = get_field('cover_image');

$image = '';

if (is_array($cover)) {
    $image = $cover['sizes']['medium'] ?? $cover['url'];
}

$summary = get_field('summary');

$excerpt = $summary ?: get_the_excerpt();

return compact(
    'title',
    'url',
    'icon',
    'excerpt',
    'image',
    'meta',
    'type'
);