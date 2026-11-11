<?php

/**
 * Get all chapter/fragment appearances for a song.
 *
 * Returns:
 *
 * [
 *   'chapter' => [
 *      'primary' => [],
 *      'secondary' => [],
 *      'supporting' => [],
 *   ],
 *
 *   'fragment' => [
 *      'primary' => [],
 *      'secondary' => [],
 *      'supporting' => [],
 *   ]
 * ]
 */
function kp_get_song_thread_roles($song_id) {
    $results = [
        'chapter'  => ['primary' => [], 'secondary' => [], 'supporting' => []],
        'fragment' => ['primary' => [], 'secondary' => [], 'supporting' => []],
        'element'  => ['all' => []] // NEW: Added elements array
    ];

    // 1. Chapters & Fragments (Existing logic preserved)
    foreach (['chapter', 'fragment'] as $post_type) {
        $posts = get_posts([
            'post_type'      => $post_type,
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ]);

        foreach ($posts as $post) {
            $songs = get_field('chapter_songs', $post->ID);
            if (empty($songs) || !is_array($songs)) continue;

            foreach ($songs as $row) {
                if (empty($row['song']) || !($row['song'] instanceof WP_Post)) continue;
                if ((int)$row['song']->ID !== (int)$song_id) continue;

                $role = $row['role'] ?? 'supporting';
                if (!isset($results[$post_type][$role])) $role = 'supporting';
                $results[$post_type][$role][] = $post;
            }
        }
    }

    // 2. Elements (NEW: Finds elements linking to this song via related_content)
    $elements = get_posts([
        'post_type'      => 'element',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => [
            [
                'key'     => 'related_content',
                'value'   => '"' . $song_id . '"',
                'compare' => 'LIKE',
            ]
        ]
    ]);
    
    foreach ($elements as $element) {
        $results['element']['all'][] = $element;
    }

    return $results;
}