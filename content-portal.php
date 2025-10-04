<?php
get_header();

the_post();

// Get all taxonomies tied to this Portal CPT
$taxonomies = get_object_taxonomies(get_post_type());
$terms = [];
foreach ($taxonomies as $taxonomy) {
    $portal_terms = wp_get_post_terms(get_the_ID(), $taxonomy);
    if (!empty($portal_terms) && !is_wp_error($portal_terms)) {
        $terms[$taxonomy] = $portal_terms;
    }
}

$cpt_sections = get_cpt_metadata(); // your central CPT helper
?>

<main class="site-main max-w-screen-lg mx-auto p-6">
  <header class="mb-8">
    <h1 class="text-4xl font-bold mb-2"><?php the_title(); ?> Portal</h1>
    <div class="text-gray-600"><?php the_excerpt(); ?></div>
  </header>

  <?php
  // Loop through CPTs and show posts that match any portal terms
  foreach ($cpt_sections as $type => $info) {
      $tax_queries = [];

      foreach ($terms as $taxonomy => $portal_terms) {
          $slugs = wp_list_pluck($portal_terms, 'slug');
          if (!empty($slugs)) {
              $tax_queries[] = [
                  'taxonomy' => $taxonomy,
                  'field'    => 'slug',
                  'terms'    => $slugs,
              ];
          }
      }

      if (empty($tax_queries)) continue;

      $query = new WP_Query([
          'post_type'      => $type,
          'posts_per_page' => -1,
          'tax_query'      => $tax_queries,
      ]);

      if ($query->have_posts()) {
          echo '<section class="mb-10">';
          echo '<h2 class="text-2xl font-semibold mb-3">' . esc_html($info['label']) . '</h2>';
          echo '<ul class="list-disc pl-6 space-y-1">';

          while ($query->have_posts()) {
              $query->the_post();
              printf(
                  '<li><a href="%s" class="text-blue-600 hover:underline">%s</a></li>',
                  esc_url(get_permalink()),
                  esc_html(get_the_title())
              );
          }

          echo '</ul></section>';
      }

      wp_reset_postdata();
  }
  ?>
</main>

<?php get_footer(); ?>
