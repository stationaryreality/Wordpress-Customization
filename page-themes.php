<?php
/*
Template Name: Themes Directory
*/
get_header(); ?>

<main style="max-width:800px; margin:0 auto; padding:24px; text-align:center;">
  <h1 style="font-size:2.5rem; font-weight:bold; margin-bottom:16px;">🎨 Themes</h1>
  <p style="margin-bottom:32px; color:#555;">
    Recurring symbolic structures that exist throughout lyrics, quotes, and other expressions.
  </p>

  <?php
  $terms = get_terms(['taxonomy' => 'theme', 'hide_empty' => false]);

  if (empty($terms) || is_wp_error($terms)) {
      echo '<p style="color:#555;">No themes found.</p>';
  } else {

      // Sort by usage count
      usort($terms, fn($a, $b) => $b->count - $a->count);

      // --- Filter top themes: exclude ones that have a portal ---
      $top_terms = [];
      foreach ($terms as $term) {
          $portal = get_page_by_title($term->name, OBJECT, 'portal');
          if (!$portal) {
              $top_terms[] = $term;
          }
          if (count($top_terms) >= 12) break; // limit top themes to 12
      }

      // --- All terms for full list ---
      $other_terms = $terms;

      // --- TOP THEMES SECTION ---
      if (!empty($top_terms)) {
          echo '<div style="margin-bottom:48px;">';
          echo '<h2 style="font-size:1.5rem; font-weight:bold; margin-bottom:8px;">🔥 Top Themes</h2>';
          echo '<p style="color:#777; margin-bottom:16px; font-style:italic;">Top Themes will likely become Portal Pages</p>';

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

      // --- ALL THEMES (A–Z) ---
      usort($other_terms, fn($a, $b) => strcasecmp($a->name, $b->name));

      echo '<div style="margin-top:32px;">';
      echo '<h2 style="font-size:1.5rem; font-weight:bold; margin-bottom:16px;">📚 All Themes (A–Z)</h2>';

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
