<?php
/**
 * Knowledge Portal Template
 * Renders sections using normalized card data.
 */
$sections = $portal_data['sections'] ?? [];
$map      = $portal_data['map'] ?? [];

/*
|--------------------------------------------------------------------------
| TEMPLATE MAP
|--------------------------------------------------------------------------
*/
$list_types = [
    'concept',
    'excerpt',
    'lyric',
    'profile',
    'quote',
];

foreach ($sections as $type => $items) {
    if (empty($items)) {
        continue;
    }

    $info = $map[$type] ?? [
        'title' => ucfirst($type),
        'emoji' => '',
    ];

    $folder = in_array($type, $list_types, true) ? 'lists' : 'grids';

    $template = locate_template("template-parts/{$folder}/{$type}.php");
    if (!$template) {
        continue;
    }

    // Standardized contract – all templates now receive the same shape
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