<?php

/**
 * Supported Knowledge CPTs
 */

function kp_get_supported_post_types(): array
{
    return [

        'concept',
        'quote',
        'song',
        'book',
        'movie',
        'excerpt',
        'lyric',
        'image',
        'element',
        'artist',
        'chapter',
        'fragment',
        'game',
        'organization',
        'portal',
        'profile',
        'show',

    ];
}


/**
 * Knowledge section order
 */

function kp_get_section_order(): array
{
    return kp_get_supported_post_types();
}


/**
 * Knowledge section labels
 */

function kp_get_section_labels(): array
{
    return [

        'concept'      => 'Concepts',
        'quote'        => 'Quotes',
        'song'         => 'Songs',
        'book'         => 'Books',
        'movie'        => 'Movies',
        'excerpt'      => 'Excerpts',
        'lyric'        => 'Lyrics',
        'image'        => 'Images',
        'element'      => 'Elements',
        'artist'       => 'Artists',
        'chapter'      => 'Narrative Threads',
        'fragment'     => 'Narrative Episodes',
        'game'         => 'Games',
        'organization' => 'Organizations',
        'portal'       => 'Portals',
        'profile'      => 'People',
        'show'         => 'TV Shows',

    ];
}