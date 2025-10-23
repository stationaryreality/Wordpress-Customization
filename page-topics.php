<?php
/*
Template Name: Topics Directory
*/
get_header(); ?>

<main style="max-width:800px; margin:0 auto; padding:24px; text-align:center;">
  <h1 style="font-size:2.5rem; font-weight:bold; margin-bottom:16px;">🧩 Topics</h1>
  <p style="margin-bottom:32px; color:#555;">
    Broad ideas and subjects — such as philosophy, politics, or culture — that connect works across the site.
  </p>

  <?php
  $terms = get_terms(['taxonomy' => 'topic', 'hide_empty' => false]);

  if (empty($terms) || is_wp_error($terms)) {
      echo '<p style="color:#555;">No topics found.</p>';
  } else {

      // Sort by usage count
      usort($terms, fn($a, $b) => $b->count - $a->count);

      // --- Filter top topics: exclude topics that already have a portal ---
      $top_terms = [];
      foreach ($terms as $term) {
          $portal = get_page_by_title($term->name, OBJECT, 'portal');
          if (!$portal) {
              $top_terms[] = $term;
          }
          if (count($top_terms) >= 12) break; // limit top topics to 12
      }

      // --- Remaining terms for full list ---
      $other_terms = $terms;

      // --- TOP TOPICS SECTION ---
      if (!empty($top_terms)) {
          echo '<div style="margin-bottom:48px;">';
          echo '<h2 style="font-size:1.5rem; font-weight:bold; margin-bottom:8px;">🔥 Top Topics</h2>';
          echo '<p style="color:#777; margin-bottom:16px; font-style:italic;">Top Topics will likely become Portal Pages</p>';

          echo '<div style="display:flex; flex-wrap:wrap; justify-content:center; gap:12px; margin-bottom:32px;">';
          foreach ($top_terms as $term) {
              $url = get_term_link($term);
              echo '<a href="' . esc_url($url) . '" style="display:inline-block; padding:6px 12px; background:#eee; border-radius:16px; text-decoration:none; color:#333; font-weight:500;">';
              echo esc_html($term->name);
              echo '</a>';
          }
          echo '</div>';
          echo '</div>';
      }

      // --- ALL TOPICS (A–Z) ---
      usort($other_terms, fn($a, $b) => strcasecmp($a->name, $b->name));

      echo '<div style="margin-top:32px;">';
      echo '<h2 style="font-size:1.5rem; font-weight:bold; margin-bottom:16px;">📚 All Topics (A–Z)</h2>';

      echo '<ul style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:12px; list-style:none; padding:0; margin:0 auto; text-align:left;">';

      foreach ($other_terms as $term) {
          $portal = get_page_by_title($term->name, OBJECT, 'portal');
          $term_link = esc_url(get_term_link($term));

          echo '<li>';
          echo '<a href="' . $term_link . '" style="color:#1a73e8; text-decoration:underline; font-weight:500;">' . esc_html($term->name) . '</a>';

          if ($portal) {
              echo ' <a href="' . get_permalink($portal->ID) . '" style="color:#777; font-size:0.85em; font-style:italic; margin-left:4px;">(Portal Page)</a>';
          }

          echo '</li>';
      }

      echo '</ul>';
      echo '</div>';
  }
  ?>
</main>

<?php get_footer(); ?>
