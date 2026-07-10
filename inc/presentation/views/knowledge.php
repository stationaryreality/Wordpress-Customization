<?php

$sections = $portal_data['sections'] ?? [];

echo '<pre>';
print_r(array_keys($sections));
echo '</pre>';
return;

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
    'template-parts/grids/' . $type,
        null,
        [
            'items' => $items,
            'type'  => $type,
            'info'  => $map[$type] ?? [],
        ]
    );

}