<?php

$cover = get_field('cover_image');
$image = '';
if (is_array($cover)) {
    $image = $cover['sizes']['medium'] ?? $cover['url'];
}

$creator = get_field('creator');
$summary = get_field('summary');
$meta    = $creator ?: '';
$excerpt = $summary ?: '';

return compact(
    'title',
    'url',
    'icon',
    'excerpt',
    'image',
    'meta',
    'type'
);