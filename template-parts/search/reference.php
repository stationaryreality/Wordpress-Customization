<?php
$query       = $args['query'];
$info        = $args['info'];
$search_term = $args['search_term'];
if(!$query->have_posts()) return;
?>

<section style="margin-bottom:4rem;">
  <h2><?php echo $info['emoji']; ?> <?php echo $info['title']; ?> containing “<?php echo esc_html($search_term); ?>”</h2>
  <div class="cited-grid">
    <?php while($query->have_posts()): $query->the_post(); ?>
      <div class="cited-item">
        <a href="<?php the_permalink(); ?>"><h3><?php the_title(); ?></h3></a>
      </div>
    <?php endwhile; ?>
  </div>
</section>
<?php wp_reset_postdata(); ?>
