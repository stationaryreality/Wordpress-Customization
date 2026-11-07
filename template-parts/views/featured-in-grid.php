<?php
$title = $args['title'] ?? 'Featured In';
$items = $args['items'] ?? [];

if (empty($items)) {
    return;
}
?>

<div class="featured-in-section">
    <h2 class="featured-in-title"><?php echo esc_html($title); ?></h2>

    <div class="featured-in-grid">
        <?php foreach ($items as $item):
            $thumb = get_the_post_thumbnail_url($item->ID, 'medium');
            
            // Fallback for chapters/fragments that use 'cover_image' instead of featured image
            if (!$thumb && in_array(get_post_type($item->ID), ['chapter', 'fragment'])) {
                $thumb = get_field('cover_image', $item->ID);
                if (is_array($thumb)) {
                    $thumb = $thumb['sizes']['medium'] ?? $thumb['url'];
                }
            }
        ?>
            <div class="featured-in-item">
                <a href="<?php echo esc_url(get_permalink($item->ID)); ?>" class="featured-in-link">
                    <?php if ($thumb): ?>
                        <img src="<?php echo esc_url($thumb); ?>"
                             alt="<?php echo esc_attr(get_the_title($item->ID)); ?>"
                             class="featured-in-image <?php echo (get_post_type($item->ID) === 'element') ? 'featured-in-image--element' : ''; ?>">
                    <?php endif; ?>
                    <h3 class="featured-in-title"><?php echo esc_html(get_the_title($item->ID)); ?></h3>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>