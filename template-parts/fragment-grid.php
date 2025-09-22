<?php
/* Template Part: Fragment Grid */

$title = $args['title'] ?? 'Narrative Fragments';

$frag_args = array(
  'post_type'      => 'fragment',
  'posts_per_page' => -1,
  'orderby'        => 'date',
  'order'          => 'DESC'
);
$frag_query = new WP_Query($frag_args);

if ($frag_query->have_posts()) : ?>
  <section style="margin-bottom:4rem;">
    <h2><?php echo esc_html($title); ?></h2>
    <div class="tag-posts-grid">
      <?php while ($frag_query->have_posts()) : $frag_query->the_post(); ?>
        <div class="tag-post-item">
          <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
            <?php if (has_post_thumbnail()) : ?>
              <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
            <?php endif; ?>
          </a>
          <a href="<?php the_permalink(); ?>" class="tag-post-title"><?php the_title(); ?></a>
          <p class="tag-post-excerpt"><?php the_excerpt(); ?></p>
        </div>
      <?php endwhile; ?>
    </div>
  </section>
  <?php wp_reset_postdata();
else :
  echo '<p>No narrative fragments found.</p>';
endif;
?>
