<?php
return [
    'key'         => 'songs',
    'enabled'     => false, // You mentioned this may just belong in the Listening Room instead.
    'priority'    => 190,
    'title'       => 'Songs',
    'emoji'       => '🎵',
    'post_type'   => 'song',
    'theme_slug'  => 'top-songs',
    'layout'      => 'grid',
    'full_page'   => '/songs-featured/', // Or point to /listening-room/ if that's the real home.
    'description' => 'Selected songs from across the archive.',
];