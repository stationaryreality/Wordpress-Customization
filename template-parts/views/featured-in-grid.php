<?php

$title = $args['title'] ?? '';
$items = $args['items'] ?? [];

if (empty($items)) {
    return;
}
?>

<div class="narrative-threads" style="margin-top: 4em; text-align:center;">

    <h2><?php echo esc_html($title); ?></h2>

    <div class="cpt-chapter-grid" style="max-width:1200px; margin:0 auto;">

        <?php foreach ($items as $item):

            $thumb = get_the_post_thumbnail_url($item->ID, 'medium');

        ?>

            <article class="cpt-chapter-grid-item">

                <a href="<?php echo get_permalink($item->ID); ?>" class="cpt-chapter-grid-link">

                    <?php if ($thumb): ?>
                        <img src="<?php echo esc_url($thumb); ?>"
                             alt="<?php echo esc_attr(get_the_title($item->ID)); ?>"
                             class="cpt-chapter-grid-image">
                    <?php endif; ?>

                    <h3 class="cpt-chapter-grid-card-title">
                        <?php echo esc_html(get_the_title($item->ID)); ?>
                    </h3>

                </a>

            </article>

        <?php endforeach; ?>

    </div>

</div>