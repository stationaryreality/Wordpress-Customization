<?php

function show_featured_in_threads($meta_key, $post_id = null) {

    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $items = get_posts([
        'post_type'      => ['chapter', 'fragment', 'element'],
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'meta_query'     => [
            [
                'key'     => $meta_key,
                'value'   => '"' . $post_id . '"',
                'compare' => 'LIKE'
            ]
        ]
    ]);

    if (!$items) {
        return;
    }

    $threads  = [];
    $elements = [];

    foreach ($items as $item) {

        $type = get_post_type($item);

        if ($type === 'element') {
            $elements[] = $item;
        } else {
            $threads[] = $item;
        }
    }

    ?>

    <div class="narrative-threads" style="margin-top:4em; text-align:center;">

        <h2>Featured In</h2>

        <?php if (!empty($threads)) : ?>

            <div class="thread-grid">

                <?php foreach ($threads as $thread) :

                    $thumb = get_the_post_thumbnail_url($thread->ID, 'medium');

                    if (!$thumb) {
                        $thumb = get_field('cover_image', $thread->ID);

                        if (is_array($thumb)) {
                            $thumb = $thumb['sizes']['medium'] ?? $thumb['url'];
                        }
                    }

                ?>

                    <div class="thread-item">

                        <a href="<?php echo esc_url(get_permalink($thread->ID)); ?>">

                            <?php if ($thumb) : ?>
                                <img src="<?php echo esc_url($thumb); ?>"
                                     alt="<?php echo esc_attr(get_the_title($thread->ID)); ?>">
                            <?php endif; ?>

                            <h3>
                                <?php echo esc_html(get_the_title($thread->ID)); ?>
                            </h3>

                        </a>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

        <?php if (!empty($elements)) : ?>

            <div style="margin-top:2.5rem;">

                <h3>Elements</h3>

                <div style="
                    display:grid;
                    grid-template-columns:repeat(auto-fill,minmax(110px,1fr));
                    gap:12px;
                    max-width:700px;
                    margin:1rem auto 0;
                ">

                    <?php foreach ($elements as $element) :

                        $image = get_field('element_image', $element->ID);

                        if (is_array($image)) {
                            $thumb = $image['sizes']['medium'] ?? $image['url'];
                        } else {
                            $thumb = get_the_post_thumbnail_url($element->ID, 'medium');
                        }

                    ?>

                        <div style="text-align:center;">

                            <a href="<?php echo esc_url(get_permalink($element->ID)); ?>">

                                <?php if ($thumb) : ?>

                                    <img src="<?php echo esc_url($thumb); ?>"
                                         alt="<?php echo esc_attr(get_the_title($element->ID)); ?>"
                                         style="
                                            width:100%;
                                            aspect-ratio:1/1;
                                            object-fit:cover;
                                            border-radius:6px;
                                            box-shadow:0 0 4px rgba(0,0,0,0.2);
                                         ">

                                <?php endif; ?>

                                <div style="
                                    font-size:0.8rem;
                                    margin-top:0.4rem;
                                    line-height:1.2;
                                ">
                                    <?php echo esc_html(get_the_title($element->ID)); ?>
                                </div>

                            </a>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

        <?php endif; ?>

    </div>

    <?php
}