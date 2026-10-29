<?php
/**
 * Template Name: Rap Songs
 */
get_header();
?>

<main id="primary" class="site-main rap-songs-page">
  <section class="rap-section rap-songs">
    <h2 class="section-heading">🎧 Rap Songs</h2>

    <?php
    $rap_songs_query = new WP_Query([
      'post_type'      => 'rap',
      'posts_per_page' => -1,
      'orderby'        => 'title',
      'order'          => 'ASC',
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
</main>

<?php get_footer(); ?>