<?php
$post_id = $post_id ?? get_the_ID();
$title   = get_the_title($post_id);
$url     = get_permalink($post_id);

$bio     = get_field('org_bio', $post_id);
$excerpt = $bio ? wp_trim_words($bio, 20) : '';

// We use 'meta' to hold the trimmed bio for the grid display
$meta_html = $excerpt; 

$cover = get_field('cover_image', $post_id);
$image = '';

if (is_array($cover)) {
    $image = $cover['sizes']['thumbnail'] ?? $cover['url'] ?? '';
} elseif (is_numeric($cover)) {
    $image = wp_get_attachment_image_url($cover, 'thumbnail');
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