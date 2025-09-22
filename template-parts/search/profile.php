<?php
$query       = $args['query'];
$info        = $args['info'];
$search_term = $args['search_term'];
if(!$query->have_posts()) return;
?>

<section style="margin-bottom:4rem;">
  <h2><?php echo $info['emoji']; ?> <?php echo $info['title']; ?> containing “<?php echo esc_html($search_term); ?>”</h2>
  <div class="author-grid">
    <?php while($query->have_posts()): $query->the_post(); 
      $portrait = get_field('portrait_image');
      $img_url  = $portrait ? $portrait['sizes']['thumbnail'] : '';
    ?>
    <div class="book-item" style="text-align:center;">
      <a href="<?php the_permalink(); ?>">
        <?php if($img_url): ?>
          <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" style="border-radius:50%; width:100px; height:100px; object-fit:cover;">
        <?php endif; ?>
        <h3><?php the_title(); ?></h3>
      </a>
    </div>
    <?php endwhile; ?>
  </div>
</section>
<?php wp_reset_postdata(); ?>
