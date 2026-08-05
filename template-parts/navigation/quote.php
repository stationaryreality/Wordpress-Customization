<?php
$current_id = get_the_ID();
$quote_ids = get_posts([
    'post_type'      => 'quote',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'fields'         => 'ids',
]);

$current_index = array_search($current_id, $quote_ids);
$next_id = $quote_ids[$current_index + 1] ?? null;
$prev_id = $quote_ids[$current_index - 1] ?? null;

function get_quote_nav_image($quote_id) {
    // 1. Reference Thumbnail
    $ref = get_field('reference_thumbnail', $quote_id);
    if ($ref) {
        if (is_numeric($ref)) return wp_get_attachment_image_url($ref, 'thumbnail');
        if (is_array($ref)) return $ref['sizes']['thumbnail'] ?? $ref['url'] ?? '';
        return $ref;
    }
    // 2. Source Image
    $source = get_field('quote_source', $quote_id);
    if ($source) {
        if (is_array($source)) $source = reset($source);
        $source_id = is_object($source) ? $source->ID : $source;
        $cover = get_field('cover_image', $source_id);
        if ($cover) {
            if (is_array($cover)) return $cover['sizes']['thumbnail'] ?? $cover['url'] ?? '';
            if (is_numeric($cover)) return wp_get_attachment_image_url($cover, 'thumbnail');
            return $cover;
        }
        if (has_post_thumbnail($source_id)) return get_the_post_thumbnail_url($source_id, 'thumbnail');
    }
    // 3. Fallback
    if (has_post_thumbnail($quote_id)) return get_the_post_thumbnail_url($quote_id, 'thumbnail');
    return get_stylesheet_directory_uri() . '/assets/images/nav-quote-library-300x157.webp';
}

$prev_image = $prev_id ? get_quote_nav_image($prev_id) : '';
$next_image = $next_id ? get_quote_nav_image($next_id) : '';
?>

<div class="cpt-quote-nav-top">
    <div class="cpt-quote-nav-row">
        <?php if ($prev_id): ?>
            <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-quote-nav-prev cpt-keyboard-nav-prev">
                <span class="cpt-quote-nav-label">← Previous Quote</span>
                <?php if ($prev_image): ?>
                    <img src="<?php echo esc_url($prev_image); ?>" alt="" class="cpt-quote-nav-thumb">
                <?php endif; ?>
                <span class="cpt-quote-nav-title"><?php echo get_the_title($prev_id); ?></span>
            </a>
        <?php endif; ?>

        <?php if ($prev_id || $next_id): ?>
            <span class="cpt-keyboard-hint-inline" title="Use arrow keys to navigate">
                Use ← ⌨️ → keys
            </span>
        <?php endif; ?>

        <?php if ($next_id): ?>
            <a href="<?php echo get_permalink($next_id); ?>" class="cpt-quote-nav-next cpt-keyboard-nav-next">
                <span class="cpt-quote-nav-label">Next Quote →</span>
                <?php if ($next_image): ?>
                    <img src="<?php echo esc_url($next_image); ?>" alt="" class="cpt-quote-nav-thumb">
                <?php endif; ?>
                <span class="cpt-quote-nav-title"><?php echo get_the_title($next_id); ?></span>
            </a>
        <?php endif; ?>
    </div>
</div>