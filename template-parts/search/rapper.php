<?php
$info        = $args['info'] ?? null;
$search_term = $args['search_term'] ?? '';
$query       = $args['query'] ?? null;

if ( ! $query || ! ( $query instanceof WP_Query ) || ! $query->have_posts() ) {
    return;
}

get_template_part(
    'template-parts/grids/rapper',
    null,
    [
        'query'       => $query,
        'title'       => $info['title'] ?? '',
        'emoji'       => $info['emoji'] ?? '',
        'search_term' => $search_term,
    ]
);