<?php
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$info         = $args['info'] ?? [];
$search_term  = $args['search_term'] ?? '';

$title = $info['title'] ?? $args['title'] ?? 'Shows';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '📺';

if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = kp_build_card('show', get_the_ID(), get_cpt_metadata());
    }
    wp_reset_postdata();
}

if (empty($items)) {
    return;
}
?>

<section class="cpt-show-section">
  <h2>
    <?php if ($emoji) echo '<span class="emoji">' . esc_html($emoji) . '</span> '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term): ?>
      <span>containing “<?php echo esc_html($search_term); ?>”</span>
    <?php endif; ?>
  </h2>

  <div class="cpt-show-grid">
    <?php foreach ($items as $item): ?>
      <?php
        // FIX: Treat meta strictly as a pre-formatted HTML string
        $meta_html = !empty($item['meta']) ? $item['meta'] : '';
        $img_url   = !empty($item['image']) ? $item['image'] : '';
      ?>
      <article class="cpt-show-grid-item">
        <a href="<?php echo esc_url($item['url']); ?>">
          <?php if ($img_url): ?>
            <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="cpt-show-grid-image">
          <?php endif; ?>
          <h3 class="cpt-show-grid-title"><?php echo esc_html($item['title']); ?></h3>
        </a>
        
        <?php if ($meta_html): ?>
          <p class="cpt-show-grid-meta"><?php echo wp_kses_post($meta_html); ?></p>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
  </div>
</section>