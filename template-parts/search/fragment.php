<?php
$query = $args['query'];
$info  = $args['info'];

get_template_part(
    'template-parts/grids/fragment',
    null,
    [
        'query'       => $query,
        'title'       => $info['title'],
        'emoji'       => $info['emoji'],
        'search_term' => $args['search_term'],
    ]
);