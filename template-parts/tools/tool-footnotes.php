<?php

/*
|--------------------------------------------------------------------------
| Footnotes Viewer
|--------------------------------------------------------------------------
|
| Displays shortcode-generated referenced works output
| for chapters and fragments.
|
*/

$post_types = [
    'chapter'  => 'Chapters',
    'fragment' => 'Fragments',
];

$selected_post_id = isset($_GET['footnote_post'])
    ? intval($_GET['footnote_post'])
    : 0;

?>

<section class="tool-footnotes-viewer">

<header class="tool-header">

    <h2>Footnotes Viewer</h2>

    <p>
        Inspect generated referenced works footnotes
        for chapters and fragments.
    </p>

</header>

<!-- ====================================================== -->
<!-- SELECTOR FORM -->
<!-- ====================================================== -->

<form method="get" class="footnotes-form">

    <!-- Preserve current tool -->

    <input type="hidden" name="tool" value="footnotes">

    <label for="footnote_post">
        Select Entry
    </label>

    <select name="footnote_post"
            id="footnote_post"
            onchange="this.form.submit()">

        <option value="">
            -- Select Chapter or Fragment --
        </option>

        <?php foreach ($post_types as $pt => $label): ?>

            <optgroup label="<?php echo esc_attr($label); ?>">

                <?php

                $posts = get_posts([
                    'post_type'      => $pt,
                    'posts_per_page' => -1,
                    'orderby'        => 'title',
                    'order'          => 'ASC',
                    'post_status'    => 'publish'
                ]);

                foreach ($posts as $p):

                ?>

                    <option value="<?php echo $p->ID; ?>"
                        <?php selected($selected_post_id, $p->ID); ?>>

                        <?php echo esc_html(get_the_title($p->ID)); ?>

                    </option>

                <?php endforeach; ?>

            </optgroup>

        <?php endforeach; ?>

    </select>

</form>

<!-- ====================================================== -->
<!-- FOOTNOTE OUTPUT -->
<!-- ====================================================== -->

<?php if ($selected_post_id): ?>

    <?php

    $selected_post = get_post($selected_post_id);

    if ($selected_post):

        setup_postdata($selected_post);

    ?>

    <div class="footnotes-viewer-output">

        <div class="footnotes-meta">

            <h3>
                <?php echo esc_html(get_the_title($selected_post_id)); ?>
            </h3>

            <p>

                <strong>Type:</strong>
                <?php echo esc_html(get_post_type($selected_post_id)); ?>

            </p>

            <p>

                <a href="<?php echo esc_url(get_permalink($selected_post_id)); ?>"
                   target="_blank">

                    View Original Entry →

                </a>

            </p>

        </div>

        <div class="footnotes-rendered">

            <?php

            /*
            |--------------------------------------------------------------------------
            | Render shortcode in proper post context
            |--------------------------------------------------------------------------
            */

            global $post;

            $post = $selected_post;

            setup_postdata($post);

            echo do_shortcode('[referenced_works]');

            wp_reset_postdata();

            ?>

        </div>

    </div>

    <?php endif; ?>

<?php endif; ?>

</section>