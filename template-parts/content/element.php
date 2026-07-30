<div <?php post_class('cpt-element-content'); ?>>

    <?php get_template_part('template-parts/navigation/element'); ?>

    <?php do_action('post_before'); ?>

    <article>
        
        <header class="post-header">
            <h1 class="post-title"><?php the_title(); ?></h1>
        </header>

        <div class="post-content">
            <?php the_content(); ?>
        </div>

        <?php show_featured_in_threads('attached_elements'); ?>

        <?php
        $related = get_field('related_content');

        if (!empty($related)) :
            $groups = [];
            foreach ($related as $item) {
                $type = get_post_type($item);
                if (in_array($type, ['chapter', 'fragment'])) continue;
                $groups[$type][] = $item;
            }

            if (!empty($groups)) :
        ?>
        <div class="cpt-element-related">
            <details open>
                <summary>Related Content</summary>
                <?php
                ksort($groups);
                foreach ($groups as $type => $items) :
                    usort($items, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));
                    $meta = get_cpt_metadata($type);
                ?>
                    <div class="cpt-element-related-group">
                        <h4>
                            <?php echo esc_html($meta['emoji'] ?? '•'); ?>
                            <?php echo esc_html($meta['title'] ?? ucfirst($type)); ?>
                            (<?php echo count($items); ?>)
                        </h4>
                        <ul>
                            <?php foreach ($items as $item) : ?>
                                <li>
                                    <a href="<?php echo esc_url(get_permalink($item)); ?>">
                                        <?php echo esc_html(get_the_title($item)); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </details>
        </div>
        <?php
            endif;
        endif;
        ?>

        <?php echo kp_render_element_sources(get_the_ID()); ?>

        <?php wp_link_pages([
            'before' => '<p class="singular-pagination">',
            'after'  => '</p>',
        ]); ?>
    </article>

    <?php do_action('post_after'); ?>

</div>