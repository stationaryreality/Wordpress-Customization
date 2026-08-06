<?php
/* Template Name: Portal Knowledge Atlas */

get_header();
the_post();

/*
|--------------------------------------------------------------------------
| DATA
|--------------------------------------------------------------------------
|
| Portal is simply one context that supplies normalized knowledge data.
| The presentation system does not need a Portal-specific data wrapper.
|
*/

$knowledge_data = kp_collect_knowledge([
    'post_id' => get_the_ID(),
]);

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
    $knowledge_data
);

get_footer();