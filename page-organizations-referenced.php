<?php
/* Template Name: Organizations Referenced */
get_header();

$orgs = new WP_Query([
  'post_type'      => 'organization',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
]);

get_template_part('template-parts/organization', 'grid', [
  'query' => $orgs,
  'title' => 'Organizations Referenced',
  'emoji' => '🏢',
]);

get_footer();
?>
