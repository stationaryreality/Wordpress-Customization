<?php
/**
 * Element (Strand) single content template.
 *
 * Three sibling regions, so the Featured In grid is never nested inside
 * the narrow reading column:
 *
 *   1. cpt-element-content            → nav, title, artist, body copy
 *   2. featured-in-section            → full-width chapter/fragment grid
 *   3. cpt-element-content--continued → related content, terms, sources
 *
 * Both reading columns keep the original <article> wrapper so theme
 * padding and measure are identical to the pre-split layout. Visual
 * order on screen is unchanged.
 */
?>

<div <?php post_class('cpt-element-content'); ?>>

    <?php get_template_part('template-parts/navigation/element'); ?>

    <?php do_action('post_before'); ?>

    <article>

        <header class="post-header">
            <h1 class="post-title"><?php the_title(); ?></h1>
        </header>

        <?php
        $primary_songs = kp_get_primary_songs(get_the_ID());
        kp_render_artist_header($primary_songs);
        ?>

        <div class="post-content">
            <?php the_content(); ?>
        </div>

    </article>

</div><!-- /.cpt-element-content — lead reading column -->


<?php
/* -----------------------------------------------------------------
   REGION 2 — Featured In
   Sibling of the reading columns, so it renders at the identical
   width it has on Song, Artist, Lyric and Excerpt pages.
   ----------------------------------------------------------------- */
show_featured_in_threads('attached_elements');
?>


<div class="cpt-element-content cpt-element-content--continued">

    <article class="cpt-element-tail">

        <?php
        $related = get_field('related_content');

        if (!empty($related)) :
            $groups = [];
            foreach ($related as $item) {
                $type = get_post_type($item);
                if (in_array($type, ['chapter', 'fragment'], true)) continue;
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
        // --- ELEMENT TERMS BUBBLES ---
        $group_titles = get_cpt_metadata();
        echo fn_element_topics(get_the_ID(), $group_titles);
        echo fn_element_themes(get_the_ID(), $group_titles);
        ?>

        <?php echo kp_render_element_sources(get_the_ID()); ?>

        <?php wp_link_pages([
            'before' => '<p class="singular-pagination">',
            'after'  => '</p>',
        ]); ?>

    </article>

    <?php do_action('post_after'); ?>

</div><!-- /.cpt-element-content--continued — tail reading column -->