<?php
/* Template Name: Custom Home */

get_header(); ?>

<main class="homepage-posts">
  <h1>Narrative Threads</h1>

  <?php
  // Posts Grid
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
        <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
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

  <hr style="margin: 4em auto; max-width: 80%; border: 0; border-top: 1px solid #ccc;">

  <?php
  // Pages Grid
  $page_args = array(
    'post_type' => 'page',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'post__not_in' => array(get_the_ID()), // Exclude the current page (Home)
    'orderby' => 'menu_order',
    'order' => 'ASC'
  );
  $pages_query = new WP_Query($page_args);

  if ($pages_query->have_posts()) :
    echo '<h2 class="page-section-title">Site Resources</h2>';
    echo '<div class="tag-posts-grid">'; // reuse the same grid class
    while ($pages_query->have_posts()) : $pages_query->the_post(); ?>
  <div class="tag-post-item">
  <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
    <?php if (has_post_thumbnail()) : ?>
      <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
    <?php endif; ?>
  </a>
  <a href="<?php the_permalink(); ?>" class="tag-post-title"><?php the_title(); ?></a>
  <p class="tag-post-excerpt"><?php the_excerpt(); ?></p>
</div>

    <?php endwhile;
    echo '</div>';
    wp_reset_postdata();
  endif;
  ?>

</main>

<?php get_footer(); ?>
