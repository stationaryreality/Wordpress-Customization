<?php
$query       = $args['query'];
$info        = $args['info'];
$search_term = $args['search_term'];
if(!$query->have_posts()) return;
?>

<section style="margin-bottom:4rem;">
  <h2><?php echo $info['emoji']; ?> <?php echo $info['title']; ?> containing “<?php echo esc_html($search_term); ?>”</h2>
  <div class="quote-list">
    <?php while($query->have_posts()): $query->the_post(); 
      $quote_html = get_field('quote_html_block');
    ?>
    <div class="quote-entry" style="margin-bottom:2rem; border-bottom:1px solid #ddd; padding-bottom:1rem;">
      <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
      <?php if($quote_html): ?>
        <p><?php echo esc_html(wp_trim_words(strip_tags($quote_html),30)); ?></p>
      <?php endif; ?>
    </div>
    <?php endwhile; ?>
  </div>
</section>
<?php wp_reset_postdata(); ?>
