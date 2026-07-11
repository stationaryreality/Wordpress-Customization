<?php

$portrait = get_field('portrait_image');
$image = '';
if (is_array($portrait)) {
    $image = $portrait['sizes']['thumbnail'] ?? $portrait['url'];
} elseif (is_numeric($portrait)) {
    $image = wp_get_attachment_image_url($portrait, 'thumbnail');
}

$bio = get_field('bio') ?: get_the_excerpt();
$excerpt = wp_trim_words($bio, 20);

// Optionally, if there's a role or title you want in meta, add it here.
// For now, leave meta empty (or set to a field like 'title').
$meta = '';

return compact(
    'title',
    'url',
    'icon',
    'excerpt',
    'image',
    'meta',
    'type'
);