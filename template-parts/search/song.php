<?php
$query       = $args['query'];
$info        = $args['info'];
$search_term = $args['search_term'];

if (!$query->have_posts()) return;

get_template_part('template-parts/song', 'grid', [
  'query'       => $query,
  'title'       => $info['title'],
  'emoji'       => $info['emoji'],
  'search_term' => $search_term,
]);
