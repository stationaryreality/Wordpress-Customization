<?php
$query = $args['query'];
$info  = $args['info'];
$search_term = $args['search_term'];

get_template_part(
    'template-parts/grids/image',
    null,
    [
        'query'    => $query,
        'title'    => $info['title'],
        'emoji'    => $info['emoji'],
        'subtitle' => 'containing “' . $search_term . '”',
    ]
);