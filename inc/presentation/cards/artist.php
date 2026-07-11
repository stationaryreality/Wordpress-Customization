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

$meta = ''; // optionally add a role/affiliation

return compact(
    'title',
    'url',
    'icon',
    'excerpt',
    'image',
    'meta',
    'type'
);