<?php
/* Template Name: Artists Featured */
get_header();
?>

<main id="primary" class="site-main artist-rapper-archive">

  <section class="cpt-section">

    <?php
    $artist_tiers = [
      'narrative'  => '📖 Narrative Artists',
      'featured'   => '🎧 Featured Artists',
      'referenced' => '🎤 Referenced Artists',
    ];

    // First: Non-rappers grouped by feature_level
    foreach ($artist_tiers as $feature_slug => $feature_label):
      ?>
      <div class="feature-group">
        <h3 class="feature-level"><?php echo esc_html($feature_label); ?></h3>

        <?php
        $non_rappers = new WP_Query([
          'post_type'      => 'artist',
          'posts_per_page' => -1,
          'tax_query'      => [
            'relation' => 'AND',
            [
              'taxonomy' => 'feature_level',
              'field'    => 'slug',
              'terms'    => $feature_slug,
            ],
            [
              'taxonomy' => 'artist_type',
              'field'    => 'slug',
              'terms'    => ['rapper'],
              'operator' => 'NOT IN',
            ],
          ],
          'orderby'        => 'title',
          'order'          => 'ASC',
        ]);

        if ($non_rappers->have_posts()): ?>
          <div class="author-grid">
            <?php while ($non_rappers->have_posts()): $non_rappers->the_post();
              $portrait = get_field('portrait_image');
              $img_url = $portrait ? $portrait['sizes']['thumbnail'] : '';
            ?>
              <div class="book-item">
                <a href="<?php the_permalink(); ?>">
                  <?php if ($img_url): ?>
                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title_attribute(); ?>">
                  <?php endif; ?>
                  <h3><?php the_title(); ?></h3>
                </a>
              </div>
            <?php endwhile; ?>
          </div>
        <?php
        endif;
        wp_reset_postdata();
        ?>

      </div> <!-- .feature-group -->

    <?php endforeach; ?>

  </section>

  <hr>

  <section class="cpt-section rappers-section">
    <h2>🎤 Rappers Featured</h2><BR>

    <?php
    // Then: Rappers grouped by feature_level
    foreach ($artist_tiers as $feature_slug => $feature_label):
      ?>
      <div class="feature-group rapper-group">
        <h3 class="feature-level"><?php echo esc_html($feature_label); ?></h3>

        <?php
        $rappers = new WP_Query([
          'post_type'      => 'artist',
          'posts_per_page' => -1,
          'tax_query'      => [
            'relation' => 'AND',
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
          'orderby'        => 'title',
          'order'          => 'ASC',
        ]);

        if ($rappers->have_posts()): ?>
          <div class="author-grid">
            <?php while ($rappers->have_posts()): $rappers->the_post();
              $portrait = get_field('portrait_image');
              $img_url = $portrait ? $portrait['sizes']['thumbnail'] : '';
            ?>
              <div class="book-item">
                <a href="<?php the_permalink(); ?>">
                  <?php if ($img_url): ?>
                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title_attribute(); ?>">
                  <?php endif; ?>
                  <h3><?php the_title(); ?></h3>
                </a>
              </div>
            <?php endwhile; ?>
          </div>
        <?php
        endif;
        wp_reset_postdata();
        ?>

      </div> <!-- .feature-group rapper-group -->

    <?php endforeach; ?>
  </section>

</main>

<?php get_footer(); ?>
