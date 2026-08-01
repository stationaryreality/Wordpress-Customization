<?php
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$info         = $args['info'] ?? [];
$search_term  = $args['search_term'] ?? '';

$title = $info['title'] ?? $args['title'] ?? 'Quotes';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '💬';

// If a WP_Query was passed, convert it to normalized cards
if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = kp_build_card('quote', get_the_ID(), get_cpt_metadata());
    }
    wp_reset_postdata();
}

if (empty($items)) {
    return;
}
?>

<section class="cpt-quote-section">
    <h2 class="cpt-quote-section-title">
        <?php if ($emoji) echo '<span class="emoji">' . esc_html($emoji) . '</span> '; ?>
        <?php echo esc_html($title); ?>
        <?php if ($search_term): ?>
            <span>containing “<?php echo esc_html($search_term); ?>”</span>
        <?php endif; ?>
    </h2>

    <div class="cpt-quote-list">
        <?php foreach ($items as $item): ?>
            <?php
            $card_image   = !empty($item['image']) ? $item['image'] : '';
            $card_excerpt = !empty($item['excerpt']) ? $item['excerpt'] : '';
            $card_meta    = !empty($item['meta']) ? $item['meta'] : '';
            ?>
            <article class="cpt-quote-list-item">
                <?php if ($card_image): ?>
                    <div class="cpt-quote-thumb">
                        <a href="<?php echo esc_url($item['url']); ?>">
                            <img src="<?php echo esc_url($card_image); ?>" alt="<?php echo esc_attr($item['title']); ?>">
                        </a>
                    </div>
                <?php endif; ?>

                <div class="cpt-quote-details">
                    <h3 class="cpt-quote-card-title">
                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                    </h3>

                    <?php if ($card_excerpt): ?>
                        <blockquote class="cpt-quote-snippet">
                            <?php echo esc_html(wp_trim_words($card_excerpt, 40, '...')); ?>
                        </blockquote>
                    <?php endif; ?>

                    <?php if ($card_meta): ?>
                        <p class="cpt-quote-meta">
                            <?php echo wp_kses_post($card_meta); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>