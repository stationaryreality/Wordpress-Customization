<?php
$post_id = $post_id ?? get_the_ID();
$title   = get_the_title($post_id);
$url     = get_permalink($post_id);
$excerpt = get_field('lyric_plain_text', $post_id);
$song    = get_field('song', $post_id);
$type    = 'lyric'; // Ensure type is explicitly set

$image = '';
$meta  = []; // Initialize as array

if ($song) {
    $song_title = get_the_title($song->ID);
    $song_url   = get_permalink($song->ID);
    
    $artist = get_field('song_artist', $song->ID);
    $artist_name = '';
    $artist_url  = '';
    
    if ($artist) {
        if (is_array($artist)) { 
            $artist = reset($artist); 
        }
        $artist_name = get_the_title($artist->ID);
        $artist_url  = get_permalink($artist->ID);
    }

    // Build unified meta ARRAY (this is what the template expects)
    $meta = [
        'song_title'  => $song_title,
        'song_url'    => $song_url,
        'artist_name' => $artist_name,
        'artist_url'  => $artist_url,
    ];

    // Get image from song
    $cover = get_field('cover_image', $song->ID);
    if ($cover && is_array($cover)) {
        $image = $cover['sizes']['medium'] ?? $cover['sizes']['thumbnail'] ?? $cover['url'] ?? '';
    } elseif (has_post_thumbnail($song->ID)) {
        $image = get_the_post_thumbnail_url($song->ID, 'medium');
    }
}

// Fallback image
if (!$image && has_post_thumbnail($post_id)) {
    $image = get_the_post_thumbnail_url($post_id, 'medium');
}

return compact('title', 'url', 'excerpt', 'image', 'meta', 'type');