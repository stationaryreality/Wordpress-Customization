<?php
/* Template Name: Custom Home */

get_header(); ?>

<main class="homepage-posts">

  <?php
  // Pages Grid - Site Resources FIRST

  // slugs to exclude
  $excluded_slugs = array(
    'subscription-confirmed',
    'unsubscribe',
    'unsubscribed',
    'manage-subscription',
    'contact',
    'privacy-policy'
  );

  $excluded_ids = get_posts(array(
    'post_type' => 'page',
    'fields' => 'ids',
    'post_name__in' => $excluded_slugs,
    'posts_per_page' => -1,
  ));

  $page_args = array(
    'post_type' => 'page',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'post__not_in' => array_merge(array(get_the_ID()), $excluded_ids), // exclude home + others
    'orderby' => 'menu_order',
    'order' => 'ASC'
  );
  $pages_query = new WP_Query($page_args);

  if ($pages_query->have_posts()) :
    echo '<h2 class="page-section-title">Site Resources</h2>';
    echo '<div class="tag-posts-grid">';
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

  <hr style="margin: 4em auto; max-width: 80%; border: 0; border-top: 1px solid #ccc;">

<section id="narrative-threads">
  <h1>Narrative Threads</h1>
  <!-- your grid or content here -->
</section>

  <?php
  // Chapters Grid
  $args = array(
    'post_type' => 'chapter',
    'posts_per_page' => -1,
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

</main>

<?php get_footer(); ?>
