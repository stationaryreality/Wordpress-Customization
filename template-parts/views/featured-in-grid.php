<?php

$title = $args['title'] ?? '';
$items = $args['items'] ?? [];

if (empty($items)) {
    return;
}
?>

<div class="narrative-threads" style="margin-top: 4em; text-align:center;">

    <h2><?php echo esc_html($title); ?></h2>

    <div class="thread-grid">

        <?php foreach ($items as $item):

            $thumb = get_the_post_thumbnail_url($item->ID, 'medium');

        ?>

            <div class="thread-item">

                <a href="<?php echo get_permalink($item->ID); ?>">

                    <?php if ($thumb): ?>
                        <img src="<?php echo esc_url($thumb); ?>"
                             alt="<?php echo esc_attr(get_the_title($item->ID)); ?>">
                    <?php endif; ?>

                    <h3><?php echo esc_html(get_the_title($item->ID)); ?></h3>

                </a>

            </div>

        <?php endforeach; ?>

    </div>

</div>