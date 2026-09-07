<div <?php post_class('cpt-element-content'); ?>>

    <?php get_template_part('template-parts/navigation/element'); ?>

    <?php do_action('post_before'); ?>

    <article>
        
        <header class="post-header">
            <h1 class="post-title"><?php the_title(); ?></h1>
        </header>

        <?php
        // Show primary song header if Element has valid context
        $element_primary_song = kp_get_element_primary_song(get_the_ID());
        if ($element_primary_song instanceof WP_Post):
            $artist_field = get_field('song_artist', $element_primary_song->ID);
            $primary_artist = $artist_field ? get_post($artist_field) : null;

            if ($primary_artist instanceof WP_Post):
                $portrait    = get_field('portrait_image', $primary_artist->ID);
                $img_url     = $portrait ? $portrait['sizes']['thumbnail'] : '';
                $artist_name = get_the_title($primary_artist->ID);
                $artist_link = get_permalink($primary_artist->ID);
                ?>
                <div class="kp-artist-meta">
                    <?php if ($img_url): ?>
                        <a href="<?php echo esc_url($artist_link); ?>">
                            <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($artist_name); ?>" class="kp-artist-thumbnail">
                        </a>
                    <?php endif; ?>

                    <h2 class="kp-artist-name">
                        <a href="<?php echo esc_url($artist_link); ?>">
                            <?php echo esc_html($artist_name); ?>
                        </a>
                    </h2>

                    <div class="kp-song-title">
                        <a href="<?php echo esc_url(get_permalink($element_primary_song->ID)); ?>">
                            <?php echo esc_html(get_the_title($element_primary_song->ID)); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

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

        <?php 
        // --- NEW ELEMENT TERMS BUBBLES ---
        $group_titles = get_cpt_metadata();
        echo fn_element_topics(get_the_ID(), $group_titles);
        echo fn_element_themes(get_the_ID(), $group_titles);
        // ---------------------------------
        ?>

        <?php echo kp_render_element_sources(get_the_ID()); ?>

        <?php wp_link_pages([
            'before' => '<p class="singular-pagination">',
            'after'  => '</p>',
        ]); ?>
    </article>

    <?php do_action('post_after'); ?>

</div>