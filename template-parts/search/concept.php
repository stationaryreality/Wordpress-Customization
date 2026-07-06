<?php
$query = $args['query'];
$info  = $args['info'];

get_template_part(
    'template-parts/lists/concept',
    null,
    [
        'query'       => $query,
        'title'       => $info['title'],
        'emoji'       => $info['emoji'],
        'search_term' => $args['search_term'],
    ]
);