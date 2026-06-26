<?php

/*
|--------------------------------------------------------------------------
| CPT ADMIN ORGANIZATION
|--------------------------------------------------------------------------
|
| 1. Groups CPTs under Content Library
| 2. Preserves alphabetical sorting behavior
|
*/

/*
|--------------------------------------------------------------------------
| REGISTER CONTENT LIBRARY MENU
|--------------------------------------------------------------------------
*/

add_action('admin_menu', function () {

    add_menu_page(
        'Content Library',
        'Content Library',
        'edit_posts',
        'content-library',
        '__return_null',
        'dashicons-archive',
        21
    );

}, 9);

/*
|--------------------------------------------------------------------------
| ADD CPT SUBMENUS
|--------------------------------------------------------------------------
*/

add_action('admin_menu', function () {

    $cpts = [

        'artist'       => 'Artists Featured',
        'book'         => 'Books Cited',
        'chapter'      => 'Narrative Threads',
        'concept'      => 'Lexicon',
        'element'      => 'Narrative Elements',
        'excerpt'      => 'Excerpts Library',
        'fragment'     => 'Narrative Episodes',
        'game'         => 'Video Games',
        'image'        => 'Images Gallery',
        'lyric'        => 'Song Excerpts',
        'movie'        => 'Movies Referenced',
        'organization' => 'Organizations',
        'portal'       => 'Portal Pages',
        'profile'      => 'People Referenced',
        'quote'        => 'Quote Library',
        'show'         => 'TV Shows Referenced',
        'song'         => 'Songs Featured',

    ];

    /*
    |--------------------------------------------------------------------------
    | Remove Original CPT Menus
    |--------------------------------------------------------------------------
    */

    foreach ($cpts as $slug => $label) {

        remove_menu_page(
            'edit.php?post_type=' . $slug
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Rebuild As Submenus
    |--------------------------------------------------------------------------
    */

    foreach ($cpts as $slug => $label) {

        add_submenu_page(
            'content-library',
            $label,
            $label,
            'edit_posts',
            'edit.php?post_type=' . $slug
        );
    }

}, 999);

/*
|--------------------------------------------------------------------------
| FORCE ALPHABETICAL SORTING
|--------------------------------------------------------------------------
*/

add_action('load-edit.php', function () {

    $screen = get_current_screen();

    if (!$screen || empty($screen->post_type)) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | CPTs To Force Alphabetical Sorting
    |--------------------------------------------------------------------------
    */

    $alphabetical_cpts = [

        'artist',
        'book',
        'concept',
        'excerpt',
        'game',
        'image',
        'lyric',
        'movie',
        'organization',
        'profile',
        'quote',
        'show',
        'song'

    ];

    /*
    |--------------------------------------------------------------------------
    | Skip Custom Ordered CPTs
    |--------------------------------------------------------------------------
    */

    if (!in_array($screen->post_type, $alphabetical_cpts)) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Only Force If User Hasn't Chosen Sort
    |--------------------------------------------------------------------------
    */

    if (!isset($_GET['orderby'])) {

        $_GET['orderby'] = 'title';
        $_GET['order']   = 'ASC';

        $url = add_query_arg([
            'post_type' => $screen->post_type,
            'orderby'   => 'title',
            'order'     => 'ASC',
        ], admin_url('edit.php'));

        wp_redirect($url);

        exit;
    }

});