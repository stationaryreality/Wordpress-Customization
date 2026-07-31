<?php
// Inherited from kp_build_card() scope: $title, $url, $icon, $post_id, $type

$excerpt = get_field('excerpt_plain_text', $post_id);
$source  = get_field('excerpt_source', $post_id);

$meta = ''; // Start with an empty string

if ($source) {
    $source_title = get_the_title($source->ID);
    $source_url   = get_permalink($source->ID);
    
    // 1. Add the Source (Book) to the meta string
    $meta = 'Source: <a href="' . esc_url($source_url) . '">' . esc_html($source_title) . '</a>';

    // 2. Check if source is a book to get the author
    if (get_post_type($source->ID) === 'book') {
        $author = get_field('author_profile', $source->ID);
        if ($author) {
            if (is_array($author)) {
                $author = reset($author);
            }
            $author_name = get_the_title($author->ID);
            $author_url  = get_permalink($author->ID);
            
            // Append the author to the meta string
            $meta .= ' by <a href="' . esc_url($author_url) . '">' . esc_html($author_name) . '</a>';
        }
    }

    // Image logic: prefers source cover, then source thumbnail, then excerpt thumbnail
    $cover = get_field('cover_image', $source->ID);
    if ($cover && is_array($cover)) {
        $image = $cover['sizes']['medium'] ?? $cover['sizes']['thumbnail'] ?? $cover['url'] ?? '';
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
    'meta', // Now contains the full "Source: [Book] by [Author]" HTML string
    'type'
);