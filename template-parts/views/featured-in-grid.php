<?php
$title     = $args['title'] ?? 'Featured In';
$chapters  = $args['chapters'] ?? [];
$fragments = $args['fragments'] ?? [];
$elements  = $args['elements'] ?? [];
$items     = $args['items'] ?? []; // Fallback for simple lists like Referenced In

$has_threads = !empty($chapters) || !empty($fragments);
$has_elements = !empty($elements);
$has_simple_items = !empty($items);

if (!$has_threads && !$has_elements && !$has_simple_items) {
    return;
}
?>

<div class="featured-in-section">
    <h2 class="featured-in-section-title"><?php echo esc_html($title); ?></h2>

    <?php if ($has_simple_items): ?>
        <div class="featured-in-grid">
            <?php foreach ($items as $item):
                $thumb = get_the_post_thumbnail_url($item->ID, 'medium');
            ?>
                <article class="featured-in-item">
                    <a href="<?php echo esc_url(get_permalink($item->ID)); ?>" class="featured-in-link">
                        <?php if ($thumb): ?>
                            <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($item->ID)); ?>" class="featured-in-image">
                        <?php endif; ?>
                        <h3 class="featured-in-item-title"><?php echo esc_html(get_the_title($item->ID)); ?></h3>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <?php if ($has_threads): ?>
            <div class="featured-in-grid">
                <?php
                $threads = array_merge($chapters, $fragments);
                foreach ($threads as $thread):
                    $thumb = get_the_post_thumbnail_url($thread->ID, 'medium');
                    if (!$thumb) {
                        $cover = get_field('cover_image', $thread->ID);
                        if (is_array($cover)) $thumb = $cover['sizes']['medium'] ?? $cover['url'];
                    }
                ?>
                    <article class="featured-in-item">
                        <a href="<?php echo esc_url(get_permalink($thread->ID)); ?>" class="featured-in-link">
                            <?php if ($thumb): ?>
                                <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($thread->ID)); ?>" class="featured-in-image">
                            <?php endif; ?>
                            <h3 class="featured-in-item-title"><?php echo esc_html(get_the_title($thread->ID)); ?></h3>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($has_elements): ?>
            <div class="featured-in-elements">
                <h3 class="featured-in-elements-title">Elements</h3>
                <div class="featured-in-grid featured-in-grid--elements">
                    <?php foreach ($elements as $element):
                        $image = get_field('element_image', $element->ID);
                        if (is_array($image)) {
                            $thumb = $image['sizes']['medium'] ?? $image['url'];
                        } else {
                            $thumb = get_the_post_thumbnail_url($element->ID, 'medium');
                        }
                    ?>
                        <article class="featured-in-item featured-in-item--element">
                            <a href="<?php echo esc_url(get_permalink($element->ID)); ?>" class="featured-in-link">
                                <?php if ($thumb): ?>
                                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($element->ID)); ?>" class="featured-in-image featured-in-image--element">
                                <?php endif; ?>
                                <h3 class="featured-in-item-title"><?php echo esc_html(get_the_title($element->ID)); ?></h3>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>