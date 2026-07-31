<?php
// 1. Handle Author Meta (Ensure it's a clean string, not an ACF array/object)
$author = get_field('author', $post_id);
$meta = '';

if ($author) {
    if (is_array($author) && isset($author->ID)) {
        // It's a Post Object/Relationship field
        $meta = get_the_title($author->ID);
    } elseif (is_string($author)) {
        // Fallback: It's just a simple text field
        $meta = $author;
    }
}

// 2. Handle Cover Image robustly (matches the Artist pattern)
$cover = get_field('cover_image', $post_id);
$image = '';

if (is_array($cover)) {
    $image = $cover['sizes']['medium'] ?? $cover['url'] ?? '';
} elseif (is_numeric($cover)) {
    $image = wp_get_attachment_image_url($cover, 'medium');
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