<?php
/**
 * Book Grid Template
 *
 * Supports two modes:
 *
 * 1. Search Mode – receives a WP_Query
 * 2. Knowledge Mode – receives normalized card arrays
 *
 * Standardized contract:
 * - items       => array
 * - query       => WP_Query|null
 * - info        => [ 'title' => '', 'emoji' => '', 'type' => '' ]
 * - search_term => string
 */
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$info         = $args['info'] ?? [];
$search_term  = $args['search_term'] ?? '';

// Backward compatibility: allow direct title/emoji from older callers
$title = $info['title'] ?? $args['title'] ?? '';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '';

// --- Restore default query fallback (legacy behavior) ---
if (!$query && empty($items)) {
    $query = new WP_Query([
        'post_type'      => 'book',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]);
}
// ---------------------------------------------------------

// If a WP_Query was passed, convert it to cards (legacy support)
if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = kp_build_card('book', get_the_ID(), get_cpt_metadata());
    }
    wp_reset_postdata();
}

// No data to render
if (empty($items)) {
    return;
}
?>

<section class="cpt-section book-grid" style="margin-bottom:4rem;">

<?php if ($title): ?>

<h2>

<?php echo esc_html(trim($emoji . ' ' . $title)); ?>

<?php if ($search_term): ?>

containing “<?php echo esc_html($search_term); ?>”

<?php endif; ?>

</h2>

<?php endif; ?>

<div class="cited-grid">

    <?php foreach ($items as $item): ?>

        <div class="cited-item">

            <a href="<?php echo esc_url($item['url']); ?>">

                <?php if (!empty($item['image'])): ?>

                    <img
                        src="<?php echo esc_url($item['image']); ?>"
                        alt="<?php echo esc_attr($item['title']); ?>">

                <?php endif; ?>

                <h3><?php echo esc_html($item['title']); ?></h3>

            </a>

            <?php if (!empty($item['meta'])): ?>

                <p><strong><?php echo esc_html($item['meta']); ?></strong></p>

            <?php endif; ?>

        </div>

    <?php endforeach; ?>

</div>

</section>