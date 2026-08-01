<?php
/**
 * Template Part: Chapter Grid
 */
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$info         = $args['info'] ?? [];
$search_term  = $args['search_term'] ?? '';

if (!$query && empty($items)) {
    $query = new WP_Query([
        'post_type'      => 'chapter',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ]);
}

$title = $info['title'] ?? $args['title'] ?? 'Narrative Threads';

if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = kp_build_card('chapter', get_the_ID(), get_cpt_metadata());
    }
    wp_reset_postdata();
}

if (empty($items)) {
    return;
}
?>

<section class="cpt-chapter-grid-section">
  <h2 class="cpt-chapter-grid-title"><?php echo esc_html($title); ?></h2>
  
  <div class="cpt-chapter-grid">
    <?php foreach ($items as $item): ?>
      <article class="cpt-chapter-grid-item">
        <a href="<?php echo esc_url($item['url']); ?>" class="cpt-chapter-grid-link">
          <?php if (!empty($item['image'])): ?>
            <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="cpt-chapter-grid-image">
          <?php endif; ?>
          <h3 class="cpt-chapter-grid-card-title"><?php echo esc_html($item['title']); ?></h3>
        </a>
      </article>
    <?php endforeach; ?>
  </div>
</section>