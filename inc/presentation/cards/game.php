<?php

$cover = get_field('cover_image');
$image = '';
if (is_array($cover)) {
    $image = $cover['sizes']['medium'] ?? $cover['url'];
}
$developer = get_field('developer');
$summary = get_field('summary');
$meta = $developer ?: '';
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