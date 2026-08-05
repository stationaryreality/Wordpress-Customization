<?php
$post_id = $post_id ?? get_the_ID();
$title   = get_the_title($post_id);
$url     = get_permalink($post_id);
$type    = 'quote'; // Defined
$icon    = '';      // Defined

$excerpt = get_field('quote_plain_text', $post_id);
$source  = get_field('quote_source', $post_id);

$image = '';
$meta  = '';

// 1. PRIORITY: ACF reference_thumbnail
$ref_thumb = get_field('reference_thumbnail', $post_id);
if ($ref_thumb) {
    if (is_numeric($ref_thumb)) {
        $image = wp_get_attachment_image_url($ref_thumb, 'medium');
    } elseif (is_array($ref_thumb)) {
        $image = $ref_thumb['sizes']['medium'] ?? $ref_thumb['url'] ?? '';
    } else {
        $image = $ref_thumb;
    }
}

// 2. PRIORITY: Source CPT Image (e.g., Book)
if (empty($image) && $source) {
    if (is_array($source)) { $source = reset($source); }
    $source_id = is_object($source) ? $source->ID : $source;
    
    $source_title = get_the_title($source_id);
    $meta = 'from <a href="' . esc_url(get_permalink($source_id)) . '">' . esc_html($source_title) . '</a>';
    
    if (get_post_type($source_id) === 'book') {
        $author = get_field('author_profile', $source_id);
        if ($author) {
            if (is_array($author)) { $author = reset($author); }
            $author_id = is_object($author) ? $author->ID : $author;
            $meta .= ' by <a href="' . esc_url(get_permalink($author_id)) . '">' . esc_html(get_the_title($author_id)) . '</a>';
        }
    }

    $cover = get_field('cover_image', $source_id);
    if ($cover) {
        if (is_array($cover)) {
            $image = $cover['sizes']['medium'] ?? $cover['sizes']['thumbnail'] ?? $cover['url'] ?? '';
        } elseif (is_numeric($cover)) {
            $image = wp_get_attachment_image_url($cover, 'medium');
        } else {
            $image = $cover;
        }
    } elseif (has_post_thumbnail($source_id)) {
        $image = get_the_post_thumbnail_url($source_id, 'medium');
    }
}

// 3. PRIORITY: Hardcoded Fallback
if (empty($image)) {
    // UPDATE PATH to match your actual child theme directory
    $image = get_stylesheet_directory_uri() . '/assets/images/nav-quote-library-300x157.webp';
}

// Final safety check
if (is_array($image) || is_object($image)) { $image = ''; }

return compact('title', 'url', 'icon', 'excerpt', 'image', 'meta', 'type');