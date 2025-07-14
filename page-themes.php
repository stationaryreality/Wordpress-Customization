<?php
/*
Template Name: Themes Directory
*/
get_header(); ?>

<main class="site-main max-w-screen-lg mx-auto p-6">
  <h1 class="text-4xl font-bold mb-4">🧵 Themes</h1>
  <p class="mb-8 text-gray-600">Recurring symbolic structures that exist throughout lyrics, quotes, and other expressions.</p>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php
      $terms = get_terms([
        'taxonomy' => 'theme',
        'hide_empty' => false,
      ]);

      foreach ($terms as $term) {
        $url = get_term_link($term);
        $desc = term_description($term);
        echo '<a href="' . esc_url($url) . '" class="block p-4 border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition">';
        echo '<h2 class="text-xl font-semibold">' . esc_html($term->name) . '</h2>';
        if ($desc) echo '<p class="text-sm text-gray-500 mt-2">' . wp_trim_words($desc, 20) . '</p>';
        echo '</a>';
      }
    ?>
  </div>
</main>

<?php get_footer(); ?>
