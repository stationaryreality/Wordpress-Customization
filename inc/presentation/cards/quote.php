<?php

// Defaults (ensuring all compact variables are defined)
$excerpt = '';
$image   = '';
$meta    = '';

$excerpt = get_field('quote_plain_text');

$source = get_field('source');
if ($source) {
    if (is_array($source)) {
        $source = reset($source);
    }
    $cover = get_field('cover_image', $source->ID);
    if ($cover && is_array($cover)) {
        $image = $cover['sizes']['medium'] ?? $cover['sizes']['thumbnail'] ?? $cover['url'];
    } elseif (has_post_thumbnail($source->ID)) {
        $image = get_the_post_thumbnail_url($source->ID, 'medium');
    }
}

if (!$image && has_post_thumbnail($post_id)) {
    $image = get_the_post_thumbnail_url($post_id, 'medium');
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