<?php
$query       = $args['query'] ?? null;
$info        = $args['info'] ?? [];
$search_term = $args['search_term'] ?? '';

if (!$query || !$query->have_posts()) return;

get_template_part('template-parts/excerpt', 'list', [
  'query' => $query,
  'title' => $info['emoji'] . ' ' . $info['title'] . ' containing "' . esc_html($search_term) . '"'
]);
