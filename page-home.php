<?php
/* Template Name: Custom Home */

get_header(); ?>

<main class="homepage-posts">
  <h1>Stationary Reality</h1>

  <?php
  $args = array(
    'post_type' => 'post',
    'posts_per_page' => -1, // show all posts
    'orderby' => 'date',
    'order' => 'DESC'
  );
  $query = new WP_Query($args);

  if ($query->have_posts()) :
    echo '<div class="tag-posts-grid">';
    while ($query->have_posts()) : $query->the_post(); ?>
      <div class="tag-post-item">
        <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
          <?php if (has_post_thumbnail()) : ?>
            <img src="<?php the_post_thumbnail_url('custom-featured'); ?>" alt="<?php the_title(); ?>">
          <?php endif; ?>
        </a>
        <a href="<?php the_permalink(); ?>" class="tag-post-title"><?php the_title(); ?></a>
        <p class="tag-post-excerpt"><?php the_excerpt(); ?></p>
      </div>
    <?php endwhile;
    echo '</div>';
    wp_reset_postdata();
  else :
    echo '<p>No posts found.</p>';
  endif;
  ?>
</main>

<?php get_footer(); ?>
