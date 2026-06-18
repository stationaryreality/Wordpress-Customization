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