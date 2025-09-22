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
      $thumb_url = '';
      if(has_post_thumbnail()){
        $thumb_url = get_the_post_thumbnail_url(get_the_ID(),'medium');
      } elseif(get_field('cover_image')){
        $cover = get_field('cover_image');
        $thumb_url = $cover['sizes']['medium'] ?? '';
      } elseif(get_field('portrait_image')){
        $portrait = get_field('portrait_image');
        $thumb_url = $portrait['sizes']['medium'] ?? '';
      } elseif(get_field('image_file')){
        $image_file = get_field('image_file');
        $thumb_url = $image_file['sizes']['medium'] ?? '';
      }
    ?>
      <div class="cited-item">
        <a href="<?php the_permalink(); ?>">
          <?php if($thumb_url): ?><img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title(); ?>"><?php endif; ?>
          <h3><?php the_title(); ?></h3>
        </a>
      </div>
    <?php endwhile; ?>
  </div>
</section>
<?php wp_reset_postdata(); ?>
