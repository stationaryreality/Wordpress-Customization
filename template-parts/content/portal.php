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
    'knowledge',
    'index',
    'list',
    'atlas',
];

if (!in_array($view, $allowed_views, true)) {
    $view = 'index';
}

?>

<?php
get_template_part(
    'template-parts/presentation/view-switcher',
    null,
    [
        'view' => $view,
    ]
);

?>


<?php

kp_render_knowledge_view(
    $view,
    $portal_data
);

get_footer();