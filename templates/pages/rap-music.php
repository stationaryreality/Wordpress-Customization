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
    $rapper_query = new WP_Query([
      'post_type'      => 'artist',
      'posts_per_page' => -1,
      'tax_query'      => [
        [
          'taxonomy' => 'artist_type',
          'field'    => 'slug',
          'terms'    => ['rapper'],
        ],
      ],
      'orderby' => 'title',
      'order'   => 'ASC',
    ]);

    if ($rapper_query->have_posts()):
    ?>
      <div class="feature-group rapper-group">
        <?php
        set_query_var('artist_query', $rapper_query);
        get_template_part('template-parts/grids/artist');
        ?>
      </div>
    <?php
    endif;
    wp_reset_postdata();
    ?>
  </section>

  <hr>

  <!-- =========================
       RAP SONGS
  ========================== -->
  <section class="rap-section rap-songs">
    <h2 class="section-heading">🎧 Rap Songs</h2>

    <?php
    $rap_songs_query = new WP_Query([
      'post_type'      => 'song',
      'posts_per_page' => -1,
      'tax_query'      => [
        [
          'taxonomy' => 'song_category',
          'field'    => 'slug',
          'terms'    => ['rap'],
        ],
      ],
      'orderby' => 'title',
      'order'   => 'ASC',
    ]);

    if ($rap_songs_query->have_posts()):
    ?>
      <div class="feature-group">
        <?php
        get_template_part('template-parts/grids/song', null, [
          'query' => $rap_songs_query,
          'title' => '',
        ]);
        ?>
      </div>
    <?php
    endif;
    wp_reset_postdata();
    ?>
  </section>

  <hr>

  <!-- =========================
       RAP LYRICS
  ========================== -->
  <section class="rap-section rap-lyrics">
    <h2 class="section-heading">🎤 Rap Lyrics</h2>

    <?php
    // Get all rap song IDs first
    $rap_song_ids = get_posts([
      'post_type'      => 'song',
      'posts_per_page' => -1,
      'fields'         => 'ids',
      'tax_query'      => [
        [
          'taxonomy' => 'song_category',
          'field'    => 'slug',
          'terms'    => ['rap'],
        ],
      ],
    ]);

    if (!empty($rap_song_ids)):
      $rap_lyrics_query = new WP_Query([
        'post_type'      => 'lyric',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => [
          [
            'key'     => 'song',
            'value'   => $rap_song_ids,
            'compare' => 'IN',
          ],
        ],
      ]);

      get_template_part('template-parts/lists/lyric', null, [
        'query' => $rap_lyrics_query,
        'title' => '',
        'emoji' => '',
      ]);
      
      wp_reset_postdata();
    else:
      echo '<p>No rap lyrics found.</p>';
    endif;
    ?>
  </section>

</main>

<?php get_footer(); ?>