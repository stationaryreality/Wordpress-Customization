<?php
/* Template Name: Artists Featured */
get_header();
?>

<main id="primary" class="site-main artist-rapper-archive">

  <!-- Artists Featured -->
  <section class="cpt-section">
    <h2 class="cpt-group-label">Artists Featured</h2>

    <?php
    $artist_tiers = [
      'primary'    => '⭐ Primary',
      'featured'   => '🎧 Featured',
      'referenced' => '🎤 Referenced',
    ];

    foreach ($artist_tiers as $level => $label):
      $artists = new WP_Query([
        'post_type'      => 'artist',
        'posts_per_page' => -1,
        'meta_key'       => 'feature_level',
        'meta_value'     => $level,
        'orderby'        => 'title',
        'order'          => 'ASC',
      ]);

      if ($artists->have_posts()): ?>
        <div class="feature-group">
          <h3 class="feature-level"><?php echo $label; ?></h3>
          <div class="author-grid">
            <?php while ($artists->have_posts()): $artists->the_post();
              $portrait = get_field('portrait_image');
              $img_url = $portrait ? $portrait['sizes']['thumbnail'] : '';
            ?>
              <div class="book-item">
                <a href="<?php the_permalink(); ?>">
                  <?php if ($img_url): ?>
                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>">
                  <?php endif; ?>
                  <h3><?php the_title(); ?></h3>
                </a>
              </div>
            <?php endwhile; ?>
          </div>
        </div>
        <?php wp_reset_postdata();
      endif;
    endforeach;
    ?>
  </section>

  <!-- Rappers Featured -->
  <section class="cpt-section">
<h2 id="rappers" class="section-heading">Rappers Featured</h2>

    <?php
    $rapper_tiers = [
      'primary'    => '⭐ Primary',
      'featured'   => '🎧 Featured',
      'referenced' => '🎤 Referenced',
    ];

    foreach ($rapper_tiers as $level => $label):
      $rappers = new WP_Query([
        'post_type'      => 'rapper',
        'posts_per_page' => -1,
        'meta_key'       => 'feature_level',
        'meta_value'     => $level,
        'orderby'        => 'title',
        'order'          => 'ASC',
      ]);

      if ($rappers->have_posts()): ?>
        <div class="feature-group">
          <h3 class="feature-level"><?php echo $label; ?></h3>
          <div class="author-grid">
            <?php while ($rappers->have_posts()): $rappers->the_post();
              $portrait = get_field('portrait_image');
              $img_url = $portrait ? $portrait['sizes']['thumbnail'] : '';
            ?>
              <div class="book-item">
                <a href="<?php the_permalink(); ?>">
                  <?php if ($img_url): ?>
                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>">
                  <?php endif; ?>
                  <h3><?php the_title(); ?></h3>
                </a>
              </div>
            <?php endwhile; ?>
          </div>
        </div>
        <?php wp_reset_postdata();
      endif;
    endforeach;
    ?>
  </section>

</main>

<?php get_footer(); ?>
