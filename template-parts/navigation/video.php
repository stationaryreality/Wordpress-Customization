<?php
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$info         = $args['info'] ?? [];
$search_term  = $args['search_term'] ?? '';

$title = $info['title'] ?? $args['title'] ?? 'Videos';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '📼';

// Support legacy WP_Query passed from page templates
if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        
        $screenshot = get_field('video_screenshot');
        $img_url = $screenshot ? $screenshot['sizes']['large'] : get_the_post_thumbnail_url(get_the_ID(), 'large');
        
        $items[] = [
            'title'   => get_the_title(),
            'url'     => get_permalink(),
            'image'   => $img_url,
            'excerpt' => '',
            'meta'    => ''
        ];
    }
    wp_reset_postdata();
}

if (empty($items)) {
    return;
}
?>

<section class="cpt-video-section">
    <h2 class="cpt-video-section-title">
        <?php if ($emoji) echo '<span class="emoji">' . esc_html($emoji) . '</span> '; ?>
        <?php echo esc_html($title); ?>
        <?php if ($search_term): ?>
            <span>containing “<?php echo esc_html($search_term); ?>”</span>
        <?php endif; ?>
    </h2>

    <div class="cpt-video-grid">
        <?php foreach ($items as $item): ?>
            <article class="cpt-video-grid-item">
                <a href="<?php echo esc_url($item['url']); ?>" class="cpt-video-grid-link">
                    <?php if (!empty($item['image'])): ?>
                        <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="cpt-video-grid-image">
                    <?php endif; ?>
                    <h3 class="cpt-video-grid-title"><?php echo esc_html($item['title']); ?></h3>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</section>