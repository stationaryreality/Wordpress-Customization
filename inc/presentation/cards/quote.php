<?php
$post_id = $post_id ?? get_the_ID();
$title   = get_the_title($post_id);
$url     = get_permalink($post_id);
$excerpt = get_field('quote_text', $post_id); // Adjust if your ACF field is named differently
$type    = 'quote';

$image = '';
$meta  = [];

// ==========================================================
// 1. PRIORITY: ACF reference_thumbnail
// ==========================================================
$ref_thumb = get_field('reference_thumbnail', $post_id);
if ($ref_thumb) {
    if (is_numeric($ref_thumb)) {
        // It's an Attachment ID
        $image = wp_get_attachment_image_url($ref_thumb, 'medium');
    } elseif (is_array($ref_thumb)) {
        // It's an Array
        $image = $ref_thumb['sizes']['medium'] ?? $ref_thumb['url'] ?? '';
    } else {
        // Assume it's already a clean URL string
        $image = $ref_thumb;
    }
}

// ==========================================================
// 2. PRIORITY: Source CPT Image (e.g., Book Cover)
// ==========================================================
if (empty($image)) {
    // CHANGE 'book' TO YOUR ACTUAL ACF FIELD NAME FOR THE SOURCE (e.g., 'source', 'book_reference')
    $source = get_field('book', $post_id); 
    
    if ($source) {
        // Safely get the source post ID whether it's an object, array, or ID
        $source_id = is_array($source) ? (is_object($source[0]) ? $source[0]->ID : $source[0]) : (is_object($source) ? $source->ID : $source);
        
        // CHANGE 'cover_image' TO YOUR ACTUAL ACF FIELD NAME ON THE SOURCE CPT
        $source_cover = get_field('cover_image', $source_id); 
        
        if ($source_cover) {
            if (is_numeric($source_cover)) {
                $image = wp_get_attachment_image_url($source_cover, 'medium');
            } elseif (is_array($source_cover)) {
                $image = $source_cover['sizes']['medium'] ?? $source_cover['url'] ?? '';
            } else {
                $image = $source_cover;
                }
        } elseif (has_post_thumbnail($source_id)) {
            $image = get_the_post_thumbnail_url($source_id, 'medium');
        }
    }
}

// ==========================================================
// 3. PRIORITY: Hardcoded Fallback
// ==========================================================
if (empty($image)) {
    // UPDATE THIS PATH to match exactly where your webp file is stored in your child theme
    $image = get_stylesheet_directory_uri() . '/wp-content/uploads/nav-quote-library-300x157.webp';
}

// ==========================================================
// FINAL SAFETY CHECK: Ensure $image is a clean string
// ==========================================================
if (is_array($image) || is_object($image)) {
    $image = ''; // Reset to empty if it's still somehow an array/object
}

return compact('title', 'url', 'excerpt', 'image', 'meta', 'type');