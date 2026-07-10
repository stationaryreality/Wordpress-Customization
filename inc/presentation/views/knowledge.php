<?php

$sections = $portal_data['sections'] ?? [];
$map      = $portal_data['map'] ?? [];

/*
|--------------------------------------------------------------------------
| DEBUG
|--------------------------------------------------------------------------
*/

echo '<pre style="background:#f8f8f8;padding:1rem;">';
echo "Sections:\n";
print_r(array_keys($sections));
echo "</pre>";

/*
|--------------------------------------------------------------------------
| TEMPLATE MAP
|--------------------------------------------------------------------------
|
| Temporary until we build the presentation resolver.
|
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

    $folder = in_array($type, $list_types, true)
        ? 'lists'
        : 'grids';

    $template = locate_template(
        "template-parts/{$folder}/{$type}.php"
    );

    echo "<pre>";
    echo "Rendering {$type}\n";
    echo "Folder: {$folder}\n";
    echo "Template: " . ($template ? 'FOUND' : 'MISSING');
    echo "</pre>";

    if (!$template) {
        continue;
    }

    get_template_part(
        "template-parts/{$folder}/{$type}",
        null,
        [
            'items'        => $items,
            'query'        => null,
            'title'        => $map[$type]['title'] ?? ucfirst($type),
            'emoji'        => $map[$type]['emoji'] ?? '',
            'search_term'  => '',
            'type'         => $type,
            'info'         => $map[$type] ?? [],
        ]
    );
}