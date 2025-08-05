<?php
/* Template Name: Songs Featured */
get_header();
?>

<main id="primary" class="site-main song-featured-archive">

  <!-- Songs Featured -->
  <section class="cpt-section">
    <h2 class="cpt-group-label">Songs Featured</h2>

    <?php
    $song_tiers = [
      'primary'    => '⭐ Primary',
      'featured'   => '🎧 Featured',
      'referenced' => '🎤 Referenced',
    ];

    foreach ($song_tiers as $level => $label):
      $songs = new WP_Query([
        'post_type'      => 'song',
        'posts_per_page' => -1,
        'meta_key'       => 'feature_level',
        'meta_value'     => $level,
        'orderby'        => 'title',
        'order'          => 'ASC',
      ]);

      if ($songs->have_posts()): ?>
        <div class="feature-group">
          <h3 class="feature-level"><?php echo $label; ?></h3>
          <div class="song-grid">
            <?php while ($songs->have_posts()): $songs->the_post();
              $cover = get_field('cover_image');
              $img_url = $cover ? $cover['sizes']['thumbnail'] : '';
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
