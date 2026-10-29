<?php
/**
 * Template Name: Rap Lyrics
 */
get_header();
?>

<main id="primary" class="site-main rap-lyrics-page">
  <section class="rap-section rap-lyrics">
    <h2 class="section-heading">🎤 Rap Lyrics</h2>

    <?php
    // Get all rap post IDs first
    $rap_song_ids = get_posts([
      'post_type'      => 'rap',
      'posts_per_page' => -1,
      'fields'         => 'ids',
    ]);

    if (!empty($rap_song_ids)):
      $rap_lyrics_query = new WP_Query([
        'post_type'      => 'lyric',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => [
          [
            'key'     => 'song', // Update this key if your ACF field on Lyrics for rap songs is named differently
            'value'   => $rap_song_ids,
            'compare' => 'IN',
          ],
        ],
      ]);

      if ($rap_lyrics_query->have_posts()) :
        get_template_part('template-parts/lists/lyric', null, [
          'query' => $rap_lyrics_query,
          'title' => '',
          'emoji' => '',
        ]);
        wp_reset_postdata();
      else :
        echo '<p>No rap lyrics found.</p>';
      endif;
      
    else:
      echo '<p>No rap songs found to pull lyrics from.</p>';
    endif;
    ?>
  </section>
</main>

<?php get_footer(); ?>