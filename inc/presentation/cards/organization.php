<?php

$cover = get_field('cover_image', $post_id);
$image = '';
if (is_array($cover)) {
    $image = $cover['sizes']['thumbnail'] ?? $cover['url'];
} elseif (is_numeric($cover)) {
    $image = wp_get_attachment_image_url($cover, 'thumbnail');
}

$bio = get_field('org_bio', $post_id);
$excerpt = $bio ? wp_trim_words($bio, 20) : '';

$wiki_slug = get_field('wikipedia_slug', $post_id);
$meta = $wiki_slug ?: ''; // optional – could store wiki slug for later use

return compact(
    'title',
    'url',
    'icon',
    'excerpt',
    'image',
    'meta',
    'type'
);