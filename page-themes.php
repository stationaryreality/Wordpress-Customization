<?php
/*
Template Name: Themes Directory
*/
get_header(); ?>

<main style="max-width:950px; margin:0 auto; padding:24px; text-align:center;">

  <h1 style="font-size:2.6rem; font-weight:bold; margin-bottom:16px;">
    🎨 Themes
  </h1>

  <p style="margin-bottom:18px; color:#555; line-height:1.7;">
    Recurring symbolic patterns, emotional currents, motifs, and poetic structures
    that appear throughout lyrics, quotes, fragments, excerpts, and other expressions across the site.
  </p>

  <!-- Cross-link -->
  <div style="margin-bottom:42px;">

    <a href="<?php echo esc_url(site_url('/topics')); ?>"
       style="
       display:inline-block;
       padding:10px 18px;
       background:#f2f2f2;
       border-radius:999px;
       text-decoration:none;
       color:#333;
       font-weight:600;
       ">
       🧩 Browse Topics
    </a>

  </div>

  <?php

  $terms = get_terms([
      'taxonomy'   => 'theme',
      'hide_empty' => false,
  ]);

  if (empty($terms) || is_wp_error($terms)) {

      echo '<p style="color:#555;">No themes found.</p>';

  } else {

      /*
      =========================================
      SORT BY USAGE
      =========================================
      */

      usort($terms, fn($a, $b) => $b->count - $a->count);

      /*
      =========================================
      BUCKETS
      =========================================
      */

      $portal_terms = [];
      $top_terms    = [];
      $remaining    = [];

      foreach ($terms as $term) {

          $portal = get_page_by_title($term->name, OBJECT, 'portal');

          /*
          =========================================
          THEME PORTALS
          =========================================
          */

          if ($portal) {

              $portal_terms[] = [
                  'term'   => $term,
                  'portal' => $portal
              ];

              continue;
          }

          /*
          =========================================
          EMERGING THEMES
          =========================================
          */

          if (count($top_terms) < 12) {

              $top_terms[] = $term;
              continue;
          }

          /*
          =========================================
          REMAINING THEMES
          =========================================
          */

          $remaining[] = $term;
      }

      /*
      =========================================
      THEME PORTALS
      =========================================
      */

      if (!empty($portal_terms)) {

          echo '<section style="margin-bottom:64px;">';

          echo '<h2 style="font-size:1.65rem; font-weight:bold; margin-bottom:10px;">
                  🌐 Theme Portals
                </h2>';

          echo '<p style="
                  color:#666;
                  max-width:760px;
                  margin:0 auto 24px auto;
                  line-height:1.7;
                ">
                  Theme Portals are fully developed symbolic hubs that gather together
                  recurring emotional patterns, motifs, aesthetics, and poetic structures
                  from across the site.
                </p>';

          echo '<div style="
                  display:flex;
                  flex-wrap:wrap;
                  justify-content:center;
                  gap:12px;
                ">';

          foreach ($portal_terms as $item) {

              $term   = $item['term'];
              $portal = $item['portal'];

              echo '<a href="' . esc_url(get_permalink($portal->ID)) . '"
                       style="
                       display:inline-block;
                       padding:10px 16px;
                       background:#ececec;
                       border-radius:18px;
                       text-decoration:none;
                       color:#222;
                       font-weight:600;
                       ">
                       ' . esc_html($term->name) . '
                    </a>';
          }

          echo '</div>';

          echo '</section>';
      }

      /*
      =========================================
      EMERGING THEMES
      =========================================
      */

      if (!empty($top_terms)) {

          echo '<section style="margin-bottom:64px;">';

          echo '<h2 style="font-size:1.65rem; font-weight:bold; margin-bottom:10px;">
                  🔥 Emerging Themes
                </h2>';

          echo '<p style="
                  color:#666;
                  max-width:760px;
                  margin:0 auto 24px auto;
                  line-height:1.7;
                ">
                  These are the most active and frequently recurring themes that have not yet
                  become Theme Portals. As symbolic patterns deepen and accumulate meaning
                  throughout the site, the strongest themes rise into the portal layer above.
                </p>';

          echo '<div style="
                  display:flex;
                  flex-wrap:wrap;
                  justify-content:center;
                  gap:12px;
                ">';

          foreach ($top_terms as $term) {

              echo '<a href="' . esc_url(get_term_link($term)) . '"
                       style="
                       display:inline-block;
                       padding:8px 14px;
                       background:#f5f5f5;
                       border-radius:16px;
                       text-decoration:none;
                       color:#333;
                       font-weight:500;
                       ">
                       ' . esc_html($term->name) . '
                    </a>';
          }

          echo '</div>';

          echo '</section>';
      }

      /*
      =========================================
      ALL OTHER THEMES
      =========================================
      */

      usort($remaining, fn($a, $b) => strcasecmp($a->name, $b->name));

      echo '<section>';

      echo '<h2 style="font-size:1.65rem; font-weight:bold; margin-bottom:10px;">
              📚 All Other Themes (A–Z)
            </h2>';

      echo '<p style="
              color:#666;
              margin-bottom:24px;
              line-height:1.7;
            ">
              The complete alphabetical archive of all remaining themes across the site.
            </p>';

      echo '<ul style="
              display:grid;
              grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));
              gap:12px;
              list-style:none;
              padding:0;
              margin:0 auto;
              text-align:left;
            ">';

      foreach ($remaining as $term) {

          echo '<li>';

          echo '<a href="' . esc_url(get_term_link($term)) . '"
                   style="
                   color:#1a73e8;
                   text-decoration:underline;
                   font-weight:500;
                   ">
                   ' . esc_html($term->name) . '
                </a>';

          echo '</li>';
      }

      echo '</ul>';

      echo '</section>';
  }
  ?>

</main>

<?php get_footer(); ?>