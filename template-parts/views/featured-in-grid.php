<?php

$title = $args['title'] ?? '';
$items = $args['items'] ?? [];

if (empty($items)) {
    return;
}
?>

<div class="featured-in-section">

    <h2 class="featured-in-section-title"><?php echo esc_html($title); ?></h2>

    <div class="featured-in-grid">

        <?php foreach ($items as $item):

            $thumb = get_the_post_thumbnail_url($item->ID, 'medium');

        ?>

            <article class="featured-in-item">

                <a href="<?php echo get_permalink($item->ID); ?>" class="featured-in-link">

                    <?php if ($thumb): ?>
                        <img src="<?php echo esc_url($thumb); ?>"
                             alt="<?php echo esc_attr(get_the_title($item->ID)); ?>"
                             class="featured-in-image">
                    <?php endif; ?>

                    <h3 class="featured-in-item-title">
                        <?php echo esc_html(get_the_title($item->ID)); ?>
                    </h3>

                </a>

            </article>

        <?php endforeach; ?>

    </div>

</div>