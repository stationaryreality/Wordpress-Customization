<?php
/**
 * References Shortcode
 *
 * Usage:
 * [references]
 */

if (!defined('ABSPATH')) {
    exit;
}

function kp_render_references_shortcode() {

    if (!function_exists('have_rows')) {
        return '';
    }

    $post_id = get_the_ID();

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

                $reference_type  = get_sub_field('reference_type');
                $reference_title = get_sub_field('reference_title');
                $reference_url   = get_sub_field('reference_url');
                $reference_note  = get_sub_field('reference_note');

                ?>

                <div class="reference-entry">

                    <?php if ($reference_title) : ?>
                        <div class="reference-title">
                            <?php echo esc_html($reference_title); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($reference_type) : ?>
                        <div class="reference-type">
                            <?php echo esc_html($reference_type); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($reference_url) : ?>
                        <div class="reference-url">
                            <a href="<?php echo esc_url($reference_url); ?>" target="_blank" rel="noopener noreferrer">
                                View Source
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ($reference_note) : ?>
                        <div class="reference-note">
                            <?php echo wp_kses_post($reference_note); ?>
                        </div>
                    <?php endif; ?>

                </div>

            <?php endwhile; ?>

        </div>

    </details>

    <?php

    return ob_get_clean();
}

add_shortcode('references', 'kp_render_references_shortcode');