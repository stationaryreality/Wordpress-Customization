<?php
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$info         = $args['info'] ?? [];
$search_term  = $args['search_term'] ?? '';

$title = $info['title'] ?? $args['title'] ?? 'Organizations';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '🏢';

if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = kp_build_card('organization', get_the_ID(), get_cpt_metadata());
    }
    wp_reset_postdata();
}

if (empty($items)) {
    return;
}
?>

<section class="cpt-organization-section">
    <h2 class="cpt-organization-section-title">
        <?php if ($emoji) echo '<span class="emoji">' . esc_html($emoji) . '</span> '; ?>
        <?php echo esc_html($title); ?>
        <?php if ($search_term): ?>
            <span>containing “<?php echo esc_html($search_term); ?>”</span>
        <?php endif; ?>
    </h2>

    <div class="cpt-organization-grid">
        <?php foreach ($items as $item): ?>
            <?php
            $meta_html = !empty($item['meta']) ? $item['meta'] : '';
            $img_url   = !empty($item['image']) ? $item['image'] : '';
            ?>
            <article class="cpt-organization-grid-item">
                <a href="<?php echo esc_url($item['url']); ?>" class="cpt-organization-grid-link">
                    <?php if ($img_url): ?>
                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="cpt-organization-grid-image">
                    <?php endif; ?>
                    <h3 class="cpt-organization-grid-title"><?php echo esc_html($item['title']); ?></h3>
                </a>
                
                <?php if ($meta_html): ?>
                    <p class="cpt-organization-grid-meta"><?php echo esc_html(wp_trim_words($meta_html, 20)); ?></p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
</section>