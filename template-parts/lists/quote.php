<?php
/**
 * Unified Quote Display (Bilingual: Supports New Normalized Cards & Legacy WP_Query)
 */

$items       = $args['items'] ?? null;
$query       = $args['query'] ?? null;
$info        = $args['info'] ?? [];
$title       = $info['title'] ?? '';
$emoji       = $info['emoji'] ?? '';
$search_term = $args['search_term'] ?? '';

// Determine data source
if (!empty($items)) {
    $is_legacy = false;
    $posts = $items; // Array of normalized card arrays
} elseif (!empty($query)) {
    $is_legacy = true;
    $posts = $query instanceof WP_Query ? $query->posts : $query;
} else {
    return; // Bail if no data provided
}

if (empty($posts)) {
    return;
}
?>

<section class="portal-section quote-list-section">
    <?php if ($title): ?>
        <h2 class="portal-section-title">
            <?php if ($emoji) echo '<span class="emoji">' . esc_html($emoji) . '</span> '; ?>
            <?php echo esc_html($title); ?>
            <?php if ($search_term) : ?>
                <span class="search-note"> — containing “<?php echo esc_html($search_term); ?>”</span>
            <?php endif; ?>
        </h2>
    <?php endif; ?>

    <div class="portal-quote-list">
        <?php foreach ($posts as $item): ?>
            
            <?php if ($is_legacy): ?>
                <!-- LEGACY PATH: $item is a WP_Post object or ID -->
                <?php
                $post_id    = is_object($item) ? $item->ID : intval($item);
                $card_title = get_the_title($post_id);
                $card_url   = get_permalink($post_id);
                $card_excerpt = get_field('quote_plain_text', $post_id);
                
                $source = get_field('quote_source', $post_id); // Note: adjust if your legacy field is 'source' instead of 'quote_source'
                $source_link = $source ? get_permalink($source->ID) : '';
                $source_title = $source ? get_the_title($source->ID) : '';
                
                $author_name = ''; $author_link = '';
                if ($source && get_post_type($source->ID) === 'book') {
                    $author = get_field('author_profile', $source->ID);
                    if ($author) {
                        if (is_array($author)) $author = reset($author);
                        $author_name = get_the_title($author->ID);
                        $author_link = get_permalink($author->ID);
                    }
                }

                // Legacy Image Logic (simplified for brevity, add your full fallback chain here if needed)
                $card_image = '';
                if ($source) {
                    $cover = get_field('cover_image', $source->ID);
                    if ($cover && is_array($cover)) {
                        $card_image = $cover['sizes']['medium'] ?? $cover['url'] ?? '';
                    } elseif (has_post_thumbnail($source->ID)) {
                        $card_image = get_the_post_thumbnail_url($source->ID, 'medium');
                    }
                }
                if (!$card_image && has_post_thumbnail($post_id)) {
                    $card_image = get_the_post_thumbnail_url($post_id, 'medium');
                }
                ?>
            <?php else: ?>
                <!-- NEW PATH: $item is a normalized card array -->
                <?php
                $card_title   = $item['title'] ?? '';
                $card_url     = $item['url'] ?? '';
                $card_excerpt = $item['excerpt'] ?? '';
                $card_image   = $item['image'] ?? '';
                $card_meta    = $item['meta'] ?? ''; // Contains "from [Book] by [Author]"
                ?>
            <?php endif; ?>

            <article class="portal-quote-item">
                <?php if (!empty($card_image)): ?>
                    <div class="quote-thumb">
                        <a href="<?php echo esc_url($is_legacy && $source ? $source_link : $card_url); ?>">
                            <img src="<?php echo esc_url($card_image); ?>" alt="<?php echo esc_attr($card_title); ?>">
                        </a>
                    </div>
                <?php endif; ?>

                <div class="quote-content">
                    <?php if ($card_title): ?>
                        <h3 class="quote-title">
                            <a href="<?php echo esc_url($card_url); ?>"><?php echo esc_html($card_title); ?></a>
                        </h3>
                    <?php endif; ?>

                    <?php if ($card_excerpt): ?>
                        <blockquote class="quote-excerpt"><?php echo esc_html(wp_trim_words($card_excerpt, 40, '...')); ?></blockquote>
                    <?php endif; ?>

                    <?php if ($is_legacy && $source): ?>
                        <p class="quote-source">
                            from <a href="<?php echo esc_url($source_link); ?>"><?php echo esc_html($source_title); ?></a>
                            <?php if ($author_name): ?>
                                &nbsp;by <a href="<?php echo esc_url($author_link); ?>"><?php echo esc_html($author_name); ?></a>
                            <?php endif; ?>
                        </p>
                    <?php elseif (!$is_legacy && !empty($card_meta)): ?>
                        <!-- New architecture simply echoes the pre-built meta string -->
                        <p class="quote-source"><?php echo wp_kses_post($card_meta); ?></p>
                    <?php endif; ?>
                </div>
            </article>

        <?php endforeach; ?>
    </div>
</section>

<?php if ($is_legacy && $query instanceof WP_Query) wp_reset_postdata(); ?>