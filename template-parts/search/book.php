<?php
$query       = $args['query'];
$info        = $args['info'];
$search_term = $args['search_term'];
if(!$query->have_posts()) return;
?>

<section style="margin-bottom:4rem;">
  <h2><?php echo $info['emoji']; ?> <?php echo $info['title']; ?> containing “<?php echo esc_html($search_term); ?>”</h2>
  <div class="cited-grid">
    <?php while($query->have_posts()): $query->the_post(); 
      $cover   = get_field('cover_image');
      $img_url = $cover ? $cover['sizes']['medium'] : '';
      $byline  = get_field('author');
      $summary = get_field('summary');
    ?>
    <div class="cited-item">
      <a href="<?php the_permalink(); ?>">
        <?php if($img_url): ?>
          <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>">
        <?php endif; ?>
        <h3><?php the_title(); ?></h3>
      </a>
      <?php if($byline): ?><p><strong><?php echo esc_html($byline); ?></strong></p><?php endif; ?>
      <?php if($summary): ?><p><?php echo esc_html(wp_trim_words($summary,25)); ?></p><?php endif; ?>
    </div>
    <?php endwhile; ?>
  </div>
</section>
<?php wp_reset_postdata(); ?>
