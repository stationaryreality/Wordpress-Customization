<?php

function kp_build_portal_sections(
    WP_Query $query,
    array $map,
    array $section_order,
    array $section_labels,
    array $context = []
)
    {

    $sections = [];

    foreach ($section_order as $type) {
        $sections[$type] = [];
    }

/*
|--------------------------------------------------------------------------
| BUILD DATA
|--------------------------------------------------------------------------
*/

if ($query->have_posts()) :

    while ($query->have_posts()) :

        $query->the_post();

        $post_id = get_the_ID();
        $type    = get_post_type();

        if ($type === 'portal') {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | DEFAULTS
        |--------------------------------------------------------------------------
        */

        $title    = get_the_title();
        $url      = get_permalink();
        $icon     = $map[$type]['emoji'] ?? '✦';
        $excerpt  = '';
        $image    = '';
        $meta     = '';

        /*
        |--------------------------------------------------------------------------
        | CONCEPTS
        |--------------------------------------------------------------------------
        */

        if ($type === 'concept') {

            $excerpt = get_field('definition');

            $image = has_post_thumbnail()
                ? get_the_post_thumbnail_url($post_id, 'medium')
                : '';
        }

        /*
        |--------------------------------------------------------------------------
        | QUOTES
        |--------------------------------------------------------------------------
        */

        elseif ($type === 'quote') {

            $excerpt = get_field('quote_plain_text');

            $source = get_field('source');

            if ($source) {

                if (is_array($source)) {
                    $source = reset($source);
                }

                $cover = get_field('cover_image', $source->ID);

                if ($cover && is_array($cover)) {

                    $image =
                        $cover['sizes']['medium']
                        ?? $cover['sizes']['thumbnail']
                        ?? $cover['url'];

                } elseif (has_post_thumbnail($source->ID)) {

                    $image = get_the_post_thumbnail_url($source->ID, 'medium');
                }
            }

            if (!$image && has_post_thumbnail($post_id)) {

                $image = get_the_post_thumbnail_url($post_id, 'medium');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SONGS
        |--------------------------------------------------------------------------
        */

        elseif ($type === 'song') {

            $artist = get_field('song_artist');

            if ($artist) {

                if (is_array($artist)) {
                    $artist = reset($artist);
                }

                $meta = get_the_title($artist->ID);
            }

            $cover = get_field('cover_image');

            $image = $cover
                ? $cover['sizes']['medium']
                : get_the_post_thumbnail_url($post_id, 'medium');
        }

        /*
        |--------------------------------------------------------------------------
        | BOOKS
        |--------------------------------------------------------------------------
        */

        elseif ($type === 'book') {

            $meta = get_field('author');

            $cover = get_field('cover_image');

            $image = $cover
                ? $cover['sizes']['medium']
                : '';
        }

        /*
        |--------------------------------------------------------------------------
        | MOVIES
        |--------------------------------------------------------------------------
        */

        elseif ($type === 'movie') {

            $cover = get_field('cover_image');

            $image = $cover
                ? $cover['sizes']['medium']
                : get_the_post_thumbnail_url($post_id, 'medium');
        }

        /*
        |--------------------------------------------------------------------------
        | EXCERPTS
        |--------------------------------------------------------------------------
        */

elseif ($type === 'excerpt') {

    $excerpt = get_field('excerpt_plain_text');

    $source = get_field('excerpt_source');

    $author_name = '';

    if ($source && get_post_type($source->ID) === 'book') {

        $author = get_field('author_profile', $source->ID);

        if ($author) {

            if (is_array($author)) {
                $author = reset($author);
            }

            $author_name = get_the_title($author->ID);
        }
    }

    $meta = $author_name;

    /*
    |--------------------------------------------------------------------------
    | IMAGE LOGIC
    |--------------------------------------------------------------------------
    |
    | Prefer:
    | 1. Source cover_image
    | 2. Source featured image
    | 3. Excerpt featured image
    |
    */

    if ($source) {

        $cover = get_field('cover_image', $source->ID);

        if ($cover && is_array($cover)) {

            $image =
                $cover['sizes']['medium']
                ?? $cover['sizes']['thumbnail']
                ?? $cover['url'];

        } elseif (has_post_thumbnail($source->ID)) {

            $image = get_the_post_thumbnail_url($source->ID, 'medium');
        }
    }

    if (!$image && has_post_thumbnail($post_id)) {

        $image = get_the_post_thumbnail_url($post_id, 'medium');
    }
}

        /*
        |--------------------------------------------------------------------------
        | LYRICS
        |--------------------------------------------------------------------------
        */

        elseif ($type === 'lyric') {

            $excerpt = get_field('lyric_plain_text');

            $song = get_field('song');

            if ($song) {

                $song_title = get_the_title($song->ID);

                $artist = get_field('song_artist', $song->ID);

                if ($artist) {

                    if (is_array($artist)) {
                        $artist = reset($artist);
                    }

                    $meta = get_the_title($artist->ID);
                }

                $cover = get_field('cover_image', $song->ID);

                if ($cover && is_array($cover)) {

                    $image =
                        $cover['sizes']['medium']
                        ?? $cover['sizes']['thumbnail']
                        ?? $cover['url'];

                } elseif (has_post_thumbnail($song->ID)) {

                    $image = get_the_post_thumbnail_url($song->ID, 'medium');
                }
            }

            if (!$image && has_post_thumbnail($post_id)) {

                $image = get_the_post_thumbnail_url($post_id, 'medium');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | IMAGES
        |--------------------------------------------------------------------------
        */

        elseif ($type === 'image') {

            $excerpt = get_field('image_caption');

            $image_field = get_field('image_file');

            $image = $image_field
                ? $image_field['sizes']['medium']
                : get_the_post_thumbnail_url($post_id, 'medium');
        }

        /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        elseif ($type === 'element') {

            $image_field = get_field('image_file') ?: get_post_thumbnail_id();

            if (is_array($image_field)) {

                $image =
                    $image_field['sizes']['medium']
                    ?? $image_field['url'];

            } elseif ($image_field) {

                $image = wp_get_attachment_image_url($image_field, 'medium');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | STORE
        |--------------------------------------------------------------------------
        */

        $sections[$type][] = [

            'title'   => $title,
            'url'     => $url,
            'icon'    => $icon,
            'excerpt' => $excerpt,
            'image'   => $image,
            'meta'    => $meta,
            'type'    => $type,
        ];

        $total_entries++;

    endwhile;

    wp_reset_postdata();

endif;

/*
|--------------------------------------------------------------------------
| ACTIVE SECTIONS
|--------------------------------------------------------------------------
*/

$active_sections = [];

foreach ($sections as $type => $entries) {

    if (!empty($entries)) {
        $active_sections[$type] = count($entries);
    }
}

    }

    wp_reset_postdata();

    return $sections;
