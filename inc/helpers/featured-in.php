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
    <div class="narrative-threads" style="margin-top:4em; text-align:center;">

        <h2>Featured In</h2>

        <?php if (!empty($threads)) : ?>
            <div class="cpt-chapter-grid" style="max-width:1200px; margin:0 auto;">
                <?php foreach ($threads as $thread) :
                    $thumb = get_the_post_thumbnail_url($thread->ID, 'medium');
                    if (!$thumb) {
                        $thumb = get_field('cover_image', $thread->ID);
                        if (is_array($thumb)) {
                            $thumb = $thumb['sizes']['medium'] ?? $thumb['url'];
                        }
                    }
                ?>
                    <article class="cpt-chapter-grid-item">
                        <a href="<?php echo esc_url(get_permalink($thread->ID)); ?>" class="cpt-chapter-grid-link">
                            <?php if ($thumb) : ?>
                                <img src="<?php echo esc_url($thumb); ?>"
                                     alt="<?php echo esc_attr(get_the_title($thread->ID)); ?>"
                                     class="cpt-chapter-grid-image">
                            <?php endif; ?>
                            <h3 class="cpt-chapter-grid-card-title">
                                <?php echo esc_html(get_the_title($thread->ID)); ?>
                            </h3>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($elements)) : ?>
            <div style="margin-top:2.5rem;">
                <h3>Elements</h3>
                <div class="cpt-chapter-grid" style="max-width:700px; margin:0 auto;">
                    <?php foreach ($elements as $element) :
                        $image = get_field('element_image', $element->ID);
                        if (is_array($image)) {
                            $thumb = $image['sizes']['medium'] ?? $image['url'];
                        } else {
                            $thumb = get_the_post_thumbnail_url($element->ID, 'medium');
                        }
                    ?>
                        <article class="cpt-chapter-grid-item cpt-element-grid-item">
                            <a href="<?php echo esc_url(get_permalink($element->ID)); ?>" class="cpt-chapter-grid-link">
                                <?php if ($thumb) : ?>
                                    <img src="<?php echo esc_url($thumb); ?>"
                                         alt="<?php echo esc_attr(get_the_title($element->ID)); ?>"
                                         class="cpt-chapter-grid-image cpt-element-grid-image">
                                <?php endif; ?>
                                <h3 class="cpt-chapter-grid-card-title">
                                    <?php echo esc_html(get_the_title($element->ID)); ?>
                                </h3>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
    <?php
}