<?php
/**
 * Template Name: Rap Music
 */
get_header();
?>

<main id="primary" class="site-main rap-music-hub">

  <!-- =========================
       RAP ARTISTS
  ========================== -->
  <section class="rap-section rap-artists">
    <h2 class="section-heading">🎤 Rap Artists</h2>

    <?php
    // reuse existing rap artists template logic
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

  <hr>

  <!-- =========================
       RAP SONGS
  ========================== -->
  <section class="rap-section rap-songs">
    <h2 class="section-heading">🎧 Rap Songs</h2>

    <?php
    $song_tiers = [
      'narrative'  => '📖 Narrative Thread Rap Songs',
      'featured'   => '🎧 Featured Rap Songs',
      'referenced' => '🎤 Referenced Rap Songs',
    ];

    foreach ($song_tiers as $slug => $label):

      $rap_songs = new WP_Query([
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
            'operator' => 'IN',
          ],
        ],
        'orderby' => 'title',
        'order'   => 'ASC',
      ]);

      if ($rap_songs->have_posts()):
    ?>
        <div class="feature-group">
          <h3 class="feature-level"><?php echo esc_html($label); ?></h3>

          <div class="song-grid">
            <?php while ($rap_songs->have_posts()): $rap_songs->the_post();

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
    <?php
      endif;

      wp_reset_postdata();

    endforeach;
    ?>
  </section>

  <hr>

  <!-- =========================
       RAP LYRICS
  ========================== -->
  <section class="rap-section rap-lyrics">
    <h2 class="section-heading">🎤 Rap Lyrics</h2>

    <?php
    $rap_lyrics_query = new WP_Query([
      'post_type'      => 'lyric',
      'posts_per_page' => -1,
      'orderby'        => 'title',
      'order'          => 'ASC',
      'tax_query'      => [
        [
          'taxonomy' => 'song_category',
          'field'    => 'slug',
          'terms'    => ['rap'],
          'operator' => 'IN',
        ],
      ],
    ]);

    get_template_part('template-parts/lyric', 'grid', [
      'query' => $rap_lyrics_query,
      'title' => '',
      'emoji' => '',
    ]);
    ?>
  </section>

</main>

<?php get_footer(); ?>