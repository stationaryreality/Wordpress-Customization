<?php
$query       = $args['query'];
$info        = $args['info'];
$search_term = $args['search_term'];

if (!$query->have_posts()) return;

get_template_part('template-parts/image', 'grid', [
  'query'       => $query,
  'title'       => $info['title'],
  'emoji'       => $info['emoji'],
  'subtitle'    => 'containing “' . $search_term . '”',
]);
