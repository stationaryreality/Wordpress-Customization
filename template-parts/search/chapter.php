<?php
$query       = $args['query'];
$info        = $args['info'];
$search_term = $args['search_term'];

get_template_part('template-parts/chapter', 'grid', [
  'query'       => $query,
  'title'       => $info['title'],
  'emoji'       => $info['emoji'],
  'search_term' => $search_term,
]);
