<?php
$query       = $args['query'] ?? null;
$items       = $args['items'] ?? [];
$info        = $args['info'] ?? [];
$search_term = $args['search_term'] ?? '';

$title = $info['title'] ?? $args['title'] ?? 'Rap Songs';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '🎧';

if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $cover = get_field('cover_image');
        $img = '';
        if (is_array($cover)) $img = $cover['sizes']['thumbnail'] ?? $cover['url'] ?? '';
        elseif (is_numeric($cover)) $img = wp_get_attachment_image_url($cover, 'thumbnail');
        $items[] = ['title' => get_the_title(), 'url' => get_permalink(), 'image' => $img];
    }
    wp_reset_postdata();
}

if (empty($items)) return;
?>
<section class="cpt-song-section">
    <h2 class="cpt-song-section-title">
        <?php if ($emoji) echo '<span class="emoji">' . esc_html($emoji) . '</span> '; ?>
        <?php echo esc_html($title); ?>
        <?php if ($search_term): ?>
            <span>containing “<?php echo esc_html($search_term); ?>”</span>
        <?php endif; ?>
    </h2>
    <div class="cpt-song-grid">
        <?php foreach ($items as $item): ?>
            <article class="cpt-song-grid-item">
                <a href="<?php echo esc_url($item['url']); ?>" class="cpt-song-grid-link">
                    <?php if (!empty($item['image'])): ?>
                        <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="cpt-song-grid-image">
                    <?php endif; ?>
                    <h3 class="cpt-song-grid-title"><?php echo esc_html($item['title']); ?></h3>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</section>