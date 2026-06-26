<?php
/**
 * Universal Sources Renderer
 *
 * This file is a complete replacement.
 * - Type is removed from all entries; note is displayed instead.
 * - Element sources are flat (no nested details).
 * - Chapter inheritance collects direct + attached Element related content,
 *   deduplicates, and renders a single flat list (one hop only).
 */

if (!defined('ABSPATH')) {
    exit;
}

/* --------------------------------------------------------------------------
   Core renderers
-------------------------------------------------------------------------- */

/**
 * Renders references inside a <details> accordion.
 * Used where legacy nested output is expected.
 */
function kp_render_references($post_id = null) {

    if (!function_exists('have_rows')) {
        return '';
    }

    $post_id = $post_id ?: get_the_ID();

    if (!$post_id || !have_rows('references', $post_id)) {
        return '';
    }

    $count = count(get_field('references', $post_id));

    ob_start();
    ?>

    <details class="content-references">

        <summary>
            Sources (<?php echo esc_html($count); ?>)
        </summary>

        <div class="content-references-inner">

            <?php while (have_rows('references', $post_id)) : the_row(); ?>

                <?php
                $label = get_sub_field('reference_label');
                $title = get_sub_field('reference_title');
                $url   = get_sub_field('reference_url');
                $note  = get_sub_field('reference_note');
                ?>

                <div class="reference-entry" style="margin-bottom:1.25em;">

                    <?php if ($label) : ?>
                        <div>
                            <strong><?php echo esc_html($label); ?></strong>
                        </div>
                    <?php endif; ?>

                    <?php if ($title) : ?>
                        <div>
                            <?php echo esc_html($title); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($url) : ?>
                        <div>
                            <a href="<?php echo esc_url($url); ?>"
                               target="_blank"
                               rel="noopener noreferrer">
                                View Source
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ($note) : ?>
                        <div>
                            <?php echo wp_kses_post($note); ?>
                        </div>
                    <?php endif; ?>

                </div>

            <?php endwhile; ?>

        </div>

    </details>

    <?php

    return ob_get_clean();
}


/**
 * Flat reference renderer (no <details> wrapper, no type).
 * Used by the collector and the Element page.
 */
function kp_render_references_flat($post_id = null) {

    if (!function_exists('have_rows')) {
        return '';
    }

    $post_id = $post_id ?: get_the_ID();

    if (!$post_id || !have_rows('references', $post_id)) {
        return '';
    }

    ob_start();

    while (have_rows('references', $post_id)) : the_row();

        $label = get_sub_field('reference_label');
        $title = get_sub_field('reference_title');
        $url   = get_sub_field('reference_url');
        $note  = get_sub_field('reference_note');

        ?>

        <div class="reference-entry" style="margin-bottom:1.25em;">

            <?php if ($label) : ?>
                <div><strong><?php echo esc_html($label); ?></strong></div>
            <?php endif; ?>

            <?php if ($title) : ?>
                <div><?php echo esc_html($title); ?></div>
            <?php endif; ?>

            <?php if ($url) : ?>
                <div>
                    <a href="<?php echo esc_url($url); ?>"
                       target="_blank"
                       rel="noopener noreferrer">
                        View Source
                    </a>
                </div>
            <?php endif; ?>

            <?php if ($note) : ?>
                <div><?php echo wp_kses_post($note); ?></div>
            <?php endif; ?>

        </div>

        <?php

    endwhile;

    return ob_get_clean();
}


/* --------------------------------------------------------------------------
   Shortcode
-------------------------------------------------------------------------- */

function kp_references_shortcode() {
    return kp_render_references(get_the_ID());
}
add_shortcode('references', 'kp_references_shortcode');


/* --------------------------------------------------------------------------
   Chapter inheritance collector (one‑hop)
-------------------------------------------------------------------------- */

/**
 * Renders a flat, deduplicated source list for a Chapter.
 *
 * Collects:
 *   – direct CPTs (quotes, excerpts, images, lyrics)
 *   – CPTs from attached Elements' related_content (one hop)
 *
 * Deduplicates by post ID and renders a single flat list.
 */
function kp_render_related_references($chapter_id) {

    if (!function_exists('get_cpt_metadata')) {
        return '';
    }

    $source_posts = [];

    /* -------------------------------------------------------------
       Step 1: Collect direct CPTs attached to the Chapter
    ------------------------------------------------------------- */

    $direct_fields = [
        'quotes_referenced',
        'excerpts_referenced',
        'images_linked',
        'lyrics_referenced',
    ];

    foreach ($direct_fields as $field_key) {
        $items = get_field($field_key, $chapter_id);
        if (!empty($items) && is_array($items)) {
            foreach ($items as $item) {
                // Only keep posts that actually have references
                if (have_rows('references', $item->ID)) {
                    $source_posts[$item->ID] = $item;
                }
            }
        }
    }

    /* -------------------------------------------------------------
       Step 2: Collect attached Elements → their related_content
       (one hop only – no deeper recursion)
    ------------------------------------------------------------- */

    $attached_elements = get_field('attached_elements', $chapter_id);

    if (!empty($attached_elements) && is_array($attached_elements)) {
        foreach ($attached_elements as $element) {
            $related = get_field('related_content', $element->ID);
            if (!empty($related) && is_array($related)) {
                foreach ($related as $item) {
                    // Only keep posts that actually have references
                    if (have_rows('references', $item->ID)) {
                        $source_posts[$item->ID] = $item;
                    }
                }
            }
        }
    }

    // No sources found
    if (empty($source_posts)) {
        return '';
    }

    /* -------------------------------------------------------------
       Step 3: Render the deduplicated list (flat)
    ------------------------------------------------------------- */

    $metadata = get_cpt_metadata();

    ob_start();
    ?>

    <div class="chapter-sources">

        <h4>Sources</h4>

        <?php foreach ($source_posts as $post) : ?>

            <?php
            $type = get_post_type($post);
            $emoji = $metadata[$type]['emoji'] ?? '📄';
            ?>

            <div class="source-group" style="margin-bottom:1.5rem;">

                <strong>
                    <?php echo esc_html($emoji . ' ' . get_the_title($post)); ?>
                </strong>

                <?php echo kp_render_references_flat($post->ID); ?>

            </div>

        <?php endforeach; ?>

    </div>

    <?php

    return ob_get_clean();
}


/* --------------------------------------------------------------------------
   Element page sources (flat, no nested accordions)
-------------------------------------------------------------------------- */

/**
 * Renders flat sources for an Element page.
 *
 * Lists each related_content item (skipping Chapters/Fragments)
 * with its title/emoji and flat references.
 */
function kp_render_element_related_sources($element_id) {

    $related = get_field('related_content', $element_id);

    if (empty($related)) {
        return '';
    }

    if (!function_exists('get_cpt_metadata')) {
        return '';
    }

    $metadata = get_cpt_metadata();

    ob_start();
    ?>

    <div class="element-sources">

        <h4>Sources</h4>

        <?php foreach ($related as $item) : ?>

            <?php
            // Skip if this post has no references
            if (!have_rows('references', $item->ID)) {
                continue;
            }

            $type = get_post_type($item);

            // Skip chapters and fragments (they act as aggregators, not sources)
            if (in_array($type, ['chapter', 'fragment'])) {
                continue;
            }

            $emoji = $metadata[$type]['emoji'] ?? '📄';
            ?>

            <div class="source-group" style="margin-bottom:1.5rem;">

                <strong>
                    <?php echo esc_html($emoji . ' ' . get_the_title($item)); ?>
                </strong>

                <?php echo kp_render_references_flat($item->ID); ?>

            </div>

        <?php endforeach; ?>

    </div>

    <?php

    return ob_get_clean();
}