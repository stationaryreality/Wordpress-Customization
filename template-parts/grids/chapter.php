<?php
/**
 * Chapter Grid Template
 */
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$section_title = $args['title'] ?? '';
$emoji        = $args['emoji'] ?? '';
$search_term  = $args['search_term'] ?? '';

if (empty($items) && !$query) {
    $query = new WP_Query([
        'post_type'      => 'chapter',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
}

if ( empty($items) && (!$query instanceof WP_Query || !$query->have_posts()) ) {
    return;
}
?>

<section class="cpt-section chapter-grid-section" style="margin-bottom:4rem;">
  <?php if ($section_title): ?>
    <h2 class="section-title">
      <?php echo esc_html(trim($emoji . ' ' . $section_title)); ?>
      <?php if ($search_term): ?>
        containing “<?php echo esc_html($search_term); ?>”
      <?php endif; ?>
    </h2>
  <?php endif; ?>

  <div class="chapter-grid">
    <?php if (!empty($items)): ?>
      <?php foreach ($items as $item): ?>
        <div class="chapter-grid-item core-card">
          <a href="<?php echo esc_url($item['url']); ?>" class="core-card-image-wrapper">
            <?php if (!empty($item['image'])): ?>
              <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="core-card-image">
            <?php endif; ?>
          </a>
          <h3 class="core-card-title">
            <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
          </h3>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <?php while ($query->have_posts()): $query->the_post(); ?>
        <div class="chapter-grid-item core-card">
          <a href="<?php the_permalink(); ?>" class="core-card-image-wrapper">
            <?php if (has_post_thumbnail()): ?>
              <img src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium')); ?>" alt="<?php the_title_attribute(); ?>" class="core-card-image">
            <?php endif; ?>
          </a>
          <h3 class="core-card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
          </h3>
        </div>
      <?php endwhile; ?>
      <?php wp_reset_postdata(); ?>
    <?php endif; ?>
  </div>
</section>