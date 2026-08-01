<?php
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$info         = $args['info'] ?? [];
$search_term  = $args['search_term'] ?? '';

$title = $info['title'] ?? $args['title'] ?? 'Movies';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '🎬';

if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = kp_build_card('movie', get_the_ID(), get_cpt_metadata());
    }
    wp_reset_postdata();
}

if (empty($items) && (!$query instanceof WP_Query || !$query->have_posts())) {
    return;
}
?>

<section class="cpt-movie-grid-section">
  <h2 class="cpt-movie-grid-title">
    <?php if ($emoji) echo '<span class="emoji">' . esc_html($emoji) . '</span> '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term): ?>
      <span>containing “<?php echo esc_html($search_term); ?>”</span>
    <?php endif; ?>
  </h2>

  <div class="cpt-movie-grid">
    <?php if (!empty($items)): ?>
      <?php foreach ($items as $item): ?>
        <?php
          // FIX: Treat meta strictly as a pre-formatted HTML string
          $meta_html = !empty($item['meta']) ? $item['meta'] : '';
          $summary   = !empty($item['excerpt']) ? $item['excerpt'] : '';
          $img_url   = !empty($item['image']) ? $item['image'] : '';
        ?>
        <article class="cpt-movie-grid-item">
          <a href="<?php echo esc_url($item['url']); ?>" class="cpt-movie-grid-link">
            <?php if ($img_url): ?>
              <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="cpt-movie-grid-image">
            <?php endif; ?>
            <h3 class="cpt-movie-grid-card-title"><?php echo esc_html($item['title']); ?></h3>
          </a>
          
          <?php if ($meta_html): ?>
            <p class="cpt-movie-grid-meta">
              <?php echo wp_kses_post($meta_html); ?>
            </p>
          <?php endif; ?>

          <?php if ($summary): ?>
            <p class="cpt-movie-grid-excerpt"><?php echo esc_html(wp_trim_words($summary, 25)); ?></p>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    <?php else: ?>
      <?php while ($query->have_posts()): $query->the_post(); ?>
        <?php
          $meta_html = get_field('director') ? 'Director: ' . esc_html(get_field('director')) : '';
          $summary   = get_field('summary');
          $cover     = get_field('cover_image');
          $img_url   = $cover ? $cover['sizes']['medium'] : '';
        ?>
        <article class="cpt-movie-grid-item">
          <a href="<?php the_permalink(); ?>" class="cpt-movie-grid-link">
            <?php if ($img_url): ?>
              <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="cpt-movie-grid-image">
            <?php endif; ?>
            <h3 class="cpt-movie-grid-card-title"><?php the_title(); ?></h3>
          </a>
          
          <?php if ($meta_html): ?>
            <p class="cpt-movie-grid-meta"><?php echo wp_kses_post($meta_html); ?></p>
          <?php endif; ?>

          <?php if ($summary): ?>
            <p class="cpt-movie-grid-excerpt"><?php echo esc_html(wp_trim_words($summary, 25)); ?></p>
          <?php endif; ?>
        </article>
      <?php endwhile; ?>
      <?php wp_reset_postdata(); ?>
    <?php endif; ?>
  </div>
</section>