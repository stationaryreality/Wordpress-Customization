<?php

$posts = $args['posts'] ?? [];
$title = $args['title'] ?? '';

if (empty($posts)) {
    return;
}
?>

<section class="content-objects-section">

    <?php if ($title): ?>
        <h2 class="content-objects-title">
            <?php echo esc_html($title); ?>
        </h2>
    <?php endif; ?>

    <div class="content-objects-grid">

        <?php foreach ($posts as $post_obj):

            $post_id = is_object($post_obj)
                ? $post_obj->ID
                : intval($post_obj);

            $content_post = get_post($post_id);

            if (!$content_post) {
                continue;
            }
        ?>

            <article class="content-object">

                <h3 class="content-object-title">
                    <a href="<?php echo esc_url(get_permalink($post_id)); ?>">
                        <?php echo esc_html(get_the_title($post_id)); ?>
                    </a>
                </h3>

                <div class="content-object-render">
                    <?php
                    echo apply_filters(
                        'the_content',
                        $content_post->post_content
                    );
                    ?>
                </div>

            </article>

        <?php endforeach; ?>

    </div>

</section>