<?php
/* Template Name: Biographies */
get_header();
?>

<main class="biographies-page">
  <h1>Biographies</h1>
<div style="margin-bottom: 2rem;"></div>

  <?php
  $args = [
      'post_type' => 'post',
      'posts_per_page' => -1,
  ];
  $query = new WP_Query($args);

  if ($query->have_posts()) :
      while ($query->have_posts()) :
          $query->the_post();
          $content = get_the_content();
          $blocks = parse_blocks($content);

          foreach ($blocks as $block) {
              $rendered = render_block($block);

              // Check if the block anchor contains id="bio-..."
              if (strpos($rendered, 'id="bio-') !== false) {
                  echo '<div class="bio-block">';
                  echo apply_filters('the_content', $rendered);
                  echo '</div>';
              }
          }
      endwhile;
      wp_reset_postdata();
  else :
      echo '<p>No biographies found.</p>';
  endif;
  ?>

</main>

<?php get_footer(); ?>
