<?php

$sections = $portal_data['sections'] ?? [];

$map = $portal_data['map'] ?? [];

/*
|--------------------------------------------------------------------------
| RENDER SECTIONS
|--------------------------------------------------------------------------
*/

foreach ($sections as $type => $items) {

    if (empty($items)) {
        continue;
    }

    get_template_part(
        'template-parts/search/' . $type,
        null,
        [
            'items' => $items,
            'type'  => $type,
            'info'  => $map[$type] ?? [],
        ]
    );

}