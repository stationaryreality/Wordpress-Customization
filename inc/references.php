<?php
/**
 * Universal Sources Renderer
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main renderer
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
                $type  = get_sub_field('reference_type');
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

                    <?php if ($type) : ?>
                        <div>
                            <em><?php echo esc_html($type); ?></em>
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
 * Shortcode
 */
function kp_references_shortcode() {
    return kp_render_references(get_the_ID());
}

add_shortcode('references', 'kp_references_shortcode');


/**
 * CPT renderer
 */

function kp_render_related_references($chapter_id) {

    if (!function_exists('get_cpt_metadata')) {
        return '';
    }

    $groups = [
        'quote'   => get_field('quotes_referenced', $chapter_id) ?: [],
        'excerpt' => get_field('excerpts_referenced', $chapter_id) ?: [],
        'image'   => get_field('images_linked', $chapter_id) ?: [],
        'lyric'   => get_field('lyrics_referenced', $chapter_id) ?: [],
    ];

    $metadata = get_cpt_metadata();

    ob_start();

    foreach ($groups as $post_type => $items) {

        if (empty($items)) {
            continue;
        }

        $found_sources = false;

        foreach ($items as $item) {

            if (have_rows('references', $item->ID)) {

                if (!$found_sources) {

                    $emoji = $metadata[$post_type]['emoji'] ?? '📄';
                    $title = $metadata[$post_type]['title'] ?? ucfirst($post_type);

                    echo "<h5>{$emoji} {$title}</h5>";

                    $found_sources = true;
                }

                echo '<div style="margin-bottom:1em;">';

                echo '<strong>' .
                    esc_html(get_the_title($item->ID)) .
                    '</strong>';

                echo kp_render_references($item->ID);

                echo '</div>';
            }
        }
    }

    return ob_get_clean();
}

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
        $type  = get_sub_field('reference_type');
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

            <?php if ($type) : ?>
                <div><em><?php echo esc_html($type); ?></em></div>
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

/**
 * Element renderer
 */

function kp_render_element_related_sources($element_id) {

    $related = get_field('related_content', $element_id);

    if (empty($related)) {
        return '';
    }

    ob_start();

    foreach ($related as $item) {

        if (!have_rows('references', $item->ID)) {
            continue;
        }

        $type = get_post_type($item);

        if (in_array($type, ['chapter', 'fragment'])) {
            continue;
        }
        
        $meta = get_cpt_metadata($type);

        echo '<div style="margin-bottom:1rem;">';

        echo '<strong>'
            . ($meta['emoji'] ?? '📄')
            . ' '
            . esc_html(get_the_title($item))
            . '</strong>';

        echo kp_render_references($item->ID);

        echo '</div>';
    }

    return ob_get_clean();
}


/**
 * Build complete footnote context.
 *
 * Expands attached Elements into their contained CPTs while
 * preventing Chapter/Fragment recursion.
 */
function kp_build_reference_context($post_id) {

    $context = [];

    // --------------------------
    // Direct Chapter/Fragment fields
    // --------------------------

    $field_map = [
        'quote'        => 'quotes_referenced',
        'excerpt'      => 'excerpts_referenced',
        'image'        => 'images_linked',
        'lyric'        => 'lyrics_referenced',
        'book'         => 'books_referenced',
        'movie'        => 'movies_referenced',
        'show'         => 'shows_referenced',
        'game'         => 'games_referenced',
        'organization' => 'organizations_referenced',
        'concept'      => 'concepts_referenced',
        'person'       => 'people_referenced',
        'video'        => 'videos_referenced',
        'profile'      => 'profiles_referenced',
        'artist'       => 'artists_referenced',
    ];

    foreach ($field_map as $type => $field) {

        $items = get_field($field, $post_id);

        if (!$items) {
            continue;
        }

        foreach ($items as $item) {
            $context[$type][$item->ID] = $item;
        }
    }

    // --------------------------
    // Attached Elements
    // --------------------------

    $elements = get_field('attached_elements', $post_id);

    if ($elements) {

        foreach ($elements as $element) {

            $related = get_field('related_content', $element->ID);

            if (!$related) {
                continue;
            }

            foreach ($related as $item) {

                $type = get_post_type($item);

                // Never recurse into narrative containers
                if (in_array($type, ['chapter', 'fragment', 'element'])) {
                    continue;
                }

                $context[$type][$item->ID] = $item;
            }
        }
    }

    return $context;
}