<?php
$post_id = $post_id ?? get_the_ID();
$title   = get_the_title($post_id);
$url     = get_permalink($post_id);
$excerpt = get_field('quote_text', $post_id); // Adjust field name if needed
$type    = 'quote';

$image = '';
$meta  = [];

// 1. Priority: ACF reference_thumbnail
$ref_thumb = get_field('reference_thumbnail', $post_id);
if ($ref_thumb) {
    $image = is_array($ref_thumb) ? ($ref_thumb['sizes']['medium'] ?? $ref_thumb['url']) : $ref_thumb;
}

// 2. Priority: Source Image (e.g., book cover)
// Note: Adjust 'book' or 'source' to match your actual ACF field name for the source CPT
if (!$image) {
    $source = get_field('book', $post_id); // Or 'source', depending on your setup
    if ($source) {
        $source_id = is_array($source) ? $source[0]->ID : $source->ID;
        $source_cover = get_field('cover_image', $source_id); // Or 'featured_image', etc.
        if ($source_cover && is_array($source_cover)) {
            $image = $source_cover['sizes']['medium'] ?? $source_cover['url'];
        } elseif (has_post_thumbnail($source_id)) {
            $image = get_the_post_thumbnail_url($source_id, 'medium');
        }
    }
}

// 3. Priority: Hardcoded Fallback
if (!$image) {
    // UPDATE THIS PATH to match where your webp file is actually stored in your theme
    $image = get_stylesheet_directory_uri() . '/wp-content/uploads/nav-quote-library-300x157.webp';
}

return compact('title', 'url', 'excerpt', 'image', 'meta', 'type');