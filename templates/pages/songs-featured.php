<?php
/* Template Name: Songs Featured */
get_header();
?>

<main id="primary" class="site-main song-featured-archive">

  <!-- Songs by Feature Level -->
  <section class="cpt-section">
    <?php
    $song_tiers = [
      'narrative'  => '📖 Narrative Thread Songs',
      'featured'   => '🎧 Featured Songs',
      'referenced' => '🎤 Referenced Songs',
    ];

    foreach ($song_tiers as $slug => $label):

      $songs = new WP_Query([
        'post_type'      => 'song',
        'posts_per_page' => -1,
        'tax_query'      => [
          [
            'taxonomy' => 'feature_level',
            'field'    => 'slug',
            'terms'    => $slug,
          ],
          [
            'taxonomy' => 'song_category',
            'field'    => 'slug',
            'terms'    => ['rap'],
            'operator' => 'NOT IN',
          ],
        ],
        'orderby' => 'title',
        'order'   => 'ASC',
      ]);

      if ($songs->have_posts()): ?>
        <div class="feature-group">
          <h3 class="feature-level"><?php echo esc_html($label); ?></h3>

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
        <?php wp_reset_postdata(); ?>
      <?php endif; ?>

    <?php endforeach; ?>
  </section>

</main>

<?php get_footer(); ?>