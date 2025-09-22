<?php
$query       = $args['query'];
$info        = $args['info'];
$search_term = $args['search_term'];
if(!$query->have_posts()) return;
?>

<section style="margin-bottom:4rem;">
  <h2><?php echo $info['emoji']; ?> <?php echo $info['title']; ?> containing “<?php echo esc_html($search_term); ?>”</h2>
  <div class="tag-posts-grid">
    <?php while($query->have_posts()): $query->the_post(); ?>
      <div class="tag-post-item">
        <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
          <?php if(has_post_thumbnail()): ?>
            <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
          <?php endif; ?>
        </a>
        <a href="<?php the_permalink(); ?>" class="tag-post-title"><?php the_title(); ?></a>
        <p class="tag-post-excerpt"><?php the_excerpt(); ?></p>
      </div>
    <?php endwhile; ?>
  </div>
</section>
<?php wp_reset_postdata(); ?>
