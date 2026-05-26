<?php
/* Template Name: Portals Directory */

get_header();

$portals = new WP_Query([
  'post_type'      => 'portal',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
]);

get_template_part('template-parts/portal', 'grid', [
  'query' => $portals,
]);

get_footer();