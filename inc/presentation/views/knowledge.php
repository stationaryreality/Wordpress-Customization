<?php
/**
 * Knowledge View
 *
 * Renders normalized knowledge sections using the site's
 * standard grid/list presentation components.
 *
 * The view is context-independent. It does not care whether
 * the knowledge data came from a Portal, taxonomy, search,
 * or another future context.
 */

$sections = $knowledge_data['sections'] ?? [];
$map      = $knowledge_data['map'] ?? [];

/*
|--------------------------------------------------------------------------
| PRESENTATION TYPE
|--------------------------------------------------------------------------
|
| Some knowledge types use list presentation while the others
| use grid presentation.
|
*/

$list_types = [
    'concept',
    'excerpt',
    'lyric',
    'profile',
    'quote',
];

/*
|--------------------------------------------------------------------------
| RENDER SECTIONS
|--------------------------------------------------------------------------
*/

foreach ($sections as $type => $items) {

    if (empty($items)) {
        continue;
    }

    $info = $map[$type] ?? [
        'title' => ucfirst($type),
        'emoji' => '',
    ];

    $folder = in_array($type, $list_types, true)
        ? 'lists'
        : 'grids';

    $template = locate_template(
        "template-parts/{$folder}/{$type}.php"
    );

    if (!$template) {
        continue;
    }

    /*
     * Standard presentation contract.
     *
     * The underlying templates can continue using the same
     * normalized item structure regardless of their source.
     */
    get_template_part(
        "template-parts/{$folder}/{$type}",
        null,
        [
            'items'       => $items,
            'query'       => null,
            'info'        => $info,
            'search_term' => '',
            'type'        => $type,
        ]
    );
}