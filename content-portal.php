<?php
/* Template Name: Portal Knowledge Atlas */

get_header();
the_post();

/*
|--------------------------------------------------------------------------
| DATA
|--------------------------------------------------------------------------
*/

require locate_template(
    'template-parts/portal/data.php'
);

/*
|--------------------------------------------------------------------------
| VIEW
|--------------------------------------------------------------------------
*/

$view = $_GET['view'] ?? 'index';

$allowed_views = [
    'index',
    'atlas',
    'list',
];

if (!in_array($view, $allowed_views, true)) {
    $view = 'index';
}

/*
|--------------------------------------------------------------------------
| CSS
|--------------------------------------------------------------------------
*/

wp_enqueue_style(
    'portal-index',
    get_stylesheet_directory_uri()
    . '/template-parts/portal/css/portal-index.css',
    [],
    filemtime(
        get_stylesheet_directory()
        . '/template-parts/portal/css/portal-index.css'
    )
);

if ($view === 'list') {

    wp_enqueue_style(
        'portal-list',
        get_stylesheet_directory_uri()
        . '/template-parts/portal/css/portal-list.css',
        [],
        filemtime(
            get_stylesheet_directory()
            . '/template-parts/portal/css/portal-list.css'
        )
    );
}

if ($view === 'atlas') {

    wp_enqueue_style(
        'portal-atlas',
        get_stylesheet_directory_uri()
        . '/template-parts/portal/css/portal-atlas.css',
        [],
        filemtime(
            get_stylesheet_directory()
            . '/template-parts/portal/css/portal-atlas.css'
        )
    );
}

?>

<div class="portal-view-switcher">

    <label>

        View:

        <select
            onchange="window.location=this.value"
        >

            <option
                value="?view=index"
                <?php selected($view, 'index'); ?>
            >
                Index
            </option>

            <option
                value="?view=list"
                <?php selected($view, 'list'); ?>
            >
                 List
            </option>

            <option
                value="?view=atlas"
                <?php selected($view, 'atlas'); ?>
            >
                Atlas
            </option>

        </select>

    </label>

</div>

<?php

include locate_template(
    "template-parts/portal/renderers/{$view}.php"
);

get_footer();