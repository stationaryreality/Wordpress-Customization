<?php

function show_featured_in_threads($meta_key, $post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    if (empty($meta_key)) {
        return;
    }

    $context = kp_build_featured_context($meta_key, $post_id);
    $threads = array_merge($context['chapters'], $context['fragments']);
    $elements = $context['elements'];

    if (empty($threads) && empty($elements)) {
        return;
    }
    ?>
    <div class="featured-in-section">
        <h2 class="featured-in-title">Featured In</h2>

        <?php if (!empty($threads)) : ?>
            <div class="featured-in-grid">
                <?php foreach ($threads as $thread) :
                    $thumb = get_the_post_thumbnail_url($thread->ID, 'medium');
                    if (!$thumb) {
                        $thumb = get_field('cover_image', $thread->ID);
                        if (is_array($thumb)) {
                            $thumb = $thumb['sizes']['medium'] ?? $thumb['url'];
                        }
                    }
                ?>
                    <div class="featured-in-item">
                        <a href="<?php echo esc_url(get_permalink($thread->ID)); ?>">
                            <?php if ($thumb) : ?>
                                <img src="<?php echo esc_url($thumb); ?>"
                                     alt="<?php echo esc_attr(get_the_title($thread->ID)); ?>"
                                     class="featured-in-image">
                            <?php endif; ?>
                            <h3 class="featured-in-title"><?php echo esc_html(get_the_title($thread->ID)); ?></h3>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($elements)) : ?>
            <div class="featured-in-elements">
                <h3>Elements</h3>
                <div class="featured-in-grid featured-in-grid--elements">
                    <?php foreach ($elements as $element) :
                        $image = get_field('element_image', $element->ID);
                        if (is_array($image)) {
                            $thumb = $image['sizes']['medium'] ?? $image['url'];
                        } else {
                            $thumb = get_the_post_thumbnail_url($element->ID, 'medium');
                        }
                    ?>
                        <div class="featured-in-item featured-in-item--element">
                            <a href="<?php echo esc_url(get_permalink($element->ID)); ?>">
                                <?php if ($thumb) : ?>
                                    <img src="<?php echo esc_url($thumb); ?>"
                                         alt="<?php echo esc_attr(get_the_title($element->ID)); ?>"
                                         class="featured-in-image featured-in-image--element">
                                <?php endif; ?>
                                <h3 class="featured-in-title"><?php echo esc_html(get_the_title($element->ID)); ?></h3>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
}