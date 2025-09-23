<?php
$query = $args['query'] ?? null;
if (!$query || !$query->have_posts()) return;
?>

<section style="margin-bottom:4rem;">
  <h2><?php echo esc_html($args['title'] ?? 'Excerpts'); ?></h2>
  <div class="excerpt-list">
    <?php while ($query->have_posts()): $query->the_post(); 
      $text   = get_field('excerpt_plain_text', get_the_ID());
      $source = get_field('excerpt_source', get_the_ID());
      $source_link  = $source ? get_permalink($source->ID) : '';
      $source_title = $source ? get_the_title($source->ID) : '';
      
      // Handle source image
      $image = '';
      if ($source) {
        $cover = get_field('cover_image', $source->ID);
        if ($cover) {
          $image = $cover['sizes']['thumbnail'];
        } elseif (has_post_thumbnail($source->ID)) {
          $image = get_the_post_thumbnail_url($source->ID, 'thumbnail');
        }
      }
    ?>
      <div class="excerpt-entry" style="display:flex; align-items:flex-start; gap:1rem; margin-bottom:2rem; border-bottom:1px solid #ddd; padding-bottom:1rem;">
        <?php if ($image): ?>
          <a href="<?php echo esc_url($source_link); ?>" class="excerpt-thumb">
            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($source_title); ?>" style="width:48px; height:48px; border-radius:50%; object-fit:cover;">
          </a>
        <?php endif; ?>
        <div class="excerpt-text">
          <h2 style="margin-bottom:0.5rem;">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
          </h2>
          <?php if ($text): ?>
            <p style="margin:0;"><?php echo esc_html(wp_trim_words($text, 30, '...')); ?></p>
          <?php endif; ?>
          <?php if ($source): ?>
            <p style="margin-top:0.5rem; font-size:0.9rem; color:#666;">
              Source: <a href="<?php echo esc_url($source_link); ?>"><?php echo esc_html($source_title); ?></a>
            </p>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</section>
<?php wp_reset_postdata(); ?>
