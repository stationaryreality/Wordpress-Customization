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
    // Directly query the new raplyric CPT!
    $rap_lyrics_query = new WP_Query([
      'post_type'      => 'raplyric',
      'posts_per_page' => -1,
      'orderby'        => 'title',
      'order'          => 'ASC',
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
    ?>
  </section>
</main>

<?php get_footer(); ?>