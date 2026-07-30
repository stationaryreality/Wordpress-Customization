<?php
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$info         = $args['info'] ?? [];
$search_term  = $args['search_term'] ?? '';

$title = $info['title'] ?? $args['title'] ?? '';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '';

if (!$query && empty($items)) {
    $query = new WP_Query([
        'post_type'      => 'book',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]);
}

if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = kp_build_card('book', get_the_ID(), get_cpt_metadata());
    }
    wp_reset_postdata();
}

if (empty($items)) {
    return;
}
?>

<section class="cpt-book-section">

<?php if ($title): ?>
    <h2>
        <?php echo esc_html(trim($emoji . ' ' . $title)); ?>
        <?php if ($search_term): ?>
            containing “<?php echo esc_html($search_term); ?>”
        <?php endif; ?>
    </h2>
<?php endif; ?>

<div class="cpt-book-grid">
    <?php foreach ($items as $item): ?>
        <div class="cpt-book-grid-item">
            <a href="<?php echo esc_url($item['url']); ?>">
                <?php if (!empty($item['image'])): ?>
                    <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="cpt-book-grid-image">
                <?php endif; ?>
                <h3 class="cpt-book-grid-title"><?php echo esc_html($item['title']); ?></h3>
            </a>
            <?php if (!empty($item['meta'])): ?>
                <p class="cpt-book-grid-meta"><strong><?php echo esc_html($item['meta']); ?></strong></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

</section>