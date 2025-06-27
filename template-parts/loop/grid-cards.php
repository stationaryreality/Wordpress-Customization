<?php
$args = get_query_var('grid_cards_args', []);
$args = wp_parse_args($args, [
  'post_type' => 'post',
  'posts_per_page' => 12,
]);

$query = new WP_Query($args);
if ($query->have_posts()) :
?>
  <div class="grid-cards">
    <?php while ($query->have_posts()) : $query->the_post(); ?>
      <div class="grid-card">
        <a href="<?php the_permalink(); ?>" class="grid-card-thumbnail">
          <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('medium'); ?>
          <?php else : ?>
            <img src="/path/to/fallback.jpg" alt="<?php the_title(); ?>">
          <?php endif; ?>
        </a>
        <div class="grid-card-content">
          <h3 class="grid-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          <p class="grid-card-excerpt"><?php echo get_the_excerpt(); ?></p>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
<?php 
  wp_reset_postdata(); 
endif;
?>
