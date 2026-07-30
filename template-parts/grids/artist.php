<?php
$query       = $args['query'] ?? null;
$items       = $args['items'] ?? [];
$info        = $args['info'] ?? [];
$search_term = $args['search_term'] ?? '';

$title = $info['title'] ?? $args['title'] ?? '';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '';

$artist_query = $args['artist_query'] ?? get_query_var('artist_query') ?? $query ?? null;
    
if (empty($items) && (!$artist_query instanceof WP_Query || !$artist_query->have_posts())) {
    return;
}
?>

<div class="cpt-artist-grid">
  <?php if (!empty($items)): ?>
    <?php foreach ($items as $item): ?>
      <?php $portrait_url = !empty($item['image']) ? $item['image'] : ''; ?>
      <div class="cpt-artist-grid-item">
        <a href="<?php echo esc_url($item['url']); ?>">
          <?php if ($portrait_url): ?>
            <img src="<?php echo esc_url($portrait_url); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="cpt-artist-grid-image">
          <?php endif; ?>
          <h3 class="cpt-artist-grid-title"><?php echo esc_html($item['title']); ?></h3>
        </a>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <?php while ($artist_query->have_posts()): $artist_query->the_post(); 
      $portrait = get_field('portrait_image');
      $img_url  = $portrait ? $portrait['sizes']['thumbnail'] : '';
    ?>
      <div class="cpt-artist-grid-item">
        <a href="<?php the_permalink(); ?>">
          <?php if ($img_url): ?>
            <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="cpt-artist-grid-image">
          <?php endif; ?>
          <h3 class="cpt-artist-grid-title"><?php the_title(); ?></h3>
        </a>
      </div>
    <?php endwhile; ?>
    <?php wp_reset_postdata(); ?>
  <?php endif; ?>
</div>