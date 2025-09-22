<?php
$info        = $args['info'];
$search_term = $args['search_term'];

$artist_query = new WP_Query([
  'post_type'      => 'artist',
  's'              => $search_term,
  'posts_per_page' => -1,
  'relevanssi'     => true,
]);

if (function_exists('relevanssi_do_query')) {
  relevanssi_do_query($artist_query);
}

if (!$artist_query->have_posts()) return;
?>

<section style="margin-bottom:4rem;">
  <h2><?php echo esc_html($info['emoji'] . ' ' . $info['title']); ?> containing “<?php echo esc_html($search_term); ?>”</h2>

  <?php 
    // Pass the query in scope directly; the template reads $artist_query directly
    set_query_var('artist_query', $artist_query);
    get_template_part('template-parts/artist-grid');
  ?>
</section>
