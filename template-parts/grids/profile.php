<?php
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$info         = $args['info'] ?? [];
$search_term  = $args['search_term'] ?? '';

$title = $info['title'] ?? $args['title'] ?? 'People Referenced';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '👤';

if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = kp_build_card('profile', get_the_ID(), get_cpt_metadata());
    }
    wp_reset_postdata();
}

if (empty($items)) {
    return;
}
?>

<section class="cpt-profile-section">
    <h2 class="cpt-profile-section-title">
        <?php if ($emoji) echo '<span class="emoji">' . esc_html($emoji) . '</span> '; ?>
        <?php echo esc_html($title); ?>
        <?php if ($search_term): ?>
            <span>containing “<?php echo esc_html($search_term); ?>”</span>
        <?php endif; ?>
    </h2>

    <div class="cpt-profile-grid">
        <?php foreach ($items as $item): ?>
            <?php $img_url = !empty($item['image']) ? $item['image'] : ''; ?>
            <article class="cpt-profile-grid-item">
                <a href="<?php echo esc_url($item['url']); ?>" class="cpt-profile-grid-link">
                    <?php if ($img_url): ?>
                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="cpt-profile-grid-image">
                    <?php endif; ?>
                    <h3 class="cpt-profile-grid-title"><?php echo esc_html($item['title']); ?></h3>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</section>