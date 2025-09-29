<?php
/*
Template Name: Topics Directory
*/
get_header(); ?>

<main class="site-main max-w-screen-lg mx-auto p-6">
  <h1 class="text-4xl font-bold mb-4">🧩 Topics</h1>
  <p class="mb-8 text-gray-600">Broad ideas and subjects — such as philosophy, politics, or culture — that connect works across the site.</p>

  <?php
  $terms = get_terms(['taxonomy' => 'topic', 'hide_empty' => false]);
  if (empty($terms) || is_wp_error($terms)) {
      echo '<p class="text-gray-600">No topics found.</p>';
  } else {
      usort($terms, fn($a,$b) => $b->count - $a->count);

      $top_terms   = array_slice($terms, 0, 20);
      $other_terms = array_slice($terms, 20);

      $grid_items = [];
      foreach ($top_terms as $term) {
          $image_id = function_exists('get_field') ? get_field('theme_cover_image', 'term_' . $term->term_id) : '';
          if (!$image_id) $image_id = 23557;

          $grid_items[] = [
              'image_id' => intval($image_id),
              'title'    => $term->name,
              'url'      => get_term_link($term),
          ];
      }

      get_template_part('template-parts/theme-grid', null, [
          'items' => $grid_items,
          'title' => 'Top Topics',
          'emoji' => '🔥'
      ]);

      if (!empty($other_terms)) {
          usort($other_terms, fn($a,$b) => strcasecmp($a->name,$b->name));
          echo '<h2 class="text-2xl font-semibold mt-12 mb-4">📚 Other Topics (A–Z)</h2>';
          echo '<ul class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">';
          foreach ($other_terms as $term) {
              printf('<li><a class="text-blue-600 hover:underline" href="%s">%s</a></li>',
                  esc_url(get_term_link($term)),
                  esc_html($term->name)
              );
          }
          echo '</ul>';
      }
  }
  ?>
</main>

<?php get_footer(); ?>
