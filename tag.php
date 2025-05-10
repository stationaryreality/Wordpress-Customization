<?php get_header(); ?>

<main class="tag-archive">
  <h1>Posts tagged: <?php single_tag_title(); ?></h1>

  <?php
  // Show both posts and chapters with this tag
  $args = array(
      'post_type' => array('chapter'), // You can include 'post' here too if needed
      'posts_per_page' => -1,
      'tag' => get_queried_object()->slug
  );
  
  $tag_posts = new WP_Query($args);
  if ($tag_posts->have_posts()) : ?>
    <div class="tag-posts-grid">
      <?php while ($tag_posts->have_posts()) : $tag_posts->the_post(); ?>
        <div class="tag-post-item">
          <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
            <?php if (has_post_thumbnail()) : ?>
              <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
            <?php else : ?>
              <img src="path/to/default-image.jpg" alt="Default Image">
            <?php endif; ?>
          </a>
          <a href="<?php the_permalink(); ?>" class="tag-post-title"><?php the_title(); ?></a>
          <p class="tag-post-excerpt"><?php the_excerpt(); ?></p>
        </div>
      <?php endwhile; ?>
    </div>
    <?php wp_reset_postdata(); ?>
  <?php else : ?>
    <p>No posts found for this tag.</p>
  <?php endif; ?>
</main>

<?php get_footer(); ?>
