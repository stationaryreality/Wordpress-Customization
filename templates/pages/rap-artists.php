<?php
/**
 * Template Name: Rap Artists
 */
get_header();
?>

<main id="primary" class="site-main rap-artists-page">
  <section class="rap-section rap-artists">
    <h2 class="section-heading">🎤 Rap Artists</h2>

    <?php
    $rapper_query = new WP_Query([
      'post_type'      => 'rapper',
      'posts_per_page' => -1,
      'orderby'        => 'title',
      'order'          => 'ASC',
    ]);

    if ($rapper_query->have_posts()):
    ?>
      <div class="feature-group rapper-group">
        <?php
        // Note: If your grid file hardcodes 'artist' logic inside it, 
        // you may want to duplicate grids/artist.php to grids/rapper.php
        set_query_var('artist_query', $rapper_query); 
        get_template_part('template-parts/grids/rapper'); 
        ?>
      </div>
    <?php
    endif;
    wp_reset_postdata();
    ?>
  </section>
</main>

<?php get_footer(); ?>