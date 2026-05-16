<?php
/* Template Name: Rap Artists */
get_header();
?>

<main id="primary" class="site-main rap-artist-archive">

  <section class="cpt-section rappers-section">
    <h2 class="section-heading">🎤 Rap Artists</h2>

    <?php
    $artist_tiers = [
      'narrative'  => '📖 Narrative Rap Artists',
      'featured'   => '🎧 Featured Rap Artists',
      'referenced' => '🎤 Referenced Rap Artists',
    ];

    foreach ($artist_tiers as $feature_slug => $feature_label):

      $rapper_query = new WP_Query([
        'post_type'      => 'artist',
        'posts_per_page' => -1,
        'tax_query'      => [
          [
            'taxonomy' => 'feature_level',
            'field'    => 'slug',
            'terms'    => $feature_slug,
          ],
          [
            'taxonomy' => 'artist_type',
            'field'    => 'slug',
            'terms'    => ['rapper'],
            'operator' => 'IN',
          ],
        ],
        'orderby' => 'title',
        'order'   => 'ASC',
      ]);

      if ($rapper_query->have_posts()):
    ?>
        <div class="feature-group rapper-group">
          <h3 class="feature-level"><?php echo esc_html($feature_label); ?></h3>

          <?php
            set_query_var('artist_query', $rapper_query);
            get_template_part('template-parts/artist-grid');
          ?>
        </div>
    <?php
      endif;

      wp_reset_postdata();

    endforeach;
    ?>

  </section>

</main>

<?php get_footer(); ?>