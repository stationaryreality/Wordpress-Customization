<?php

/*
|--------------------------------------------------------------------------
| Plaintext Content Viewer
|--------------------------------------------------------------------------
|
| Lightweight raw-text viewer for core narrative/content CPTs.
|
| Initial supported CPTs:
|
| - quote
| - lyric
| - concept
| - excerpt
|
| Outputs:
| - clean readable plaintext
| - minimal metadata
| - no heavy inspector data
| - optimized for reading/exporting/copying
|
*/

$cpts = [
    'quote'   => '💬 Quote Library',
    'lyric'   => '🎼 Song Excerpts',
    'concept' => '🔎 Lexicon',
    'excerpt' => '📖 Excerpts Library',
];

$selected_cpt = isset($_GET['viewer_cpt'])
    ? sanitize_text_field($_GET['viewer_cpt'])
    : '';

$selected_post_id = isset($_GET['viewer_post'])
    ? intval($_GET['viewer_post'])
    : 0;

?>

<section class="tool-plaintext-viewer">

<header class="tool-header">

    <h2>Plaintext Content Viewer</h2>

    <p>
        Minimal raw-content reader for narrative and text-based CPTs.
    </p>

</header>

<!-- ====================================================== -->
<!-- SELECTOR -->
<!-- ====================================================== -->

<form method="get" class="plaintext-form">

    <input type="hidden" name="tool" value="plaintext">

    <!-- ============================================== -->
    <!-- CPT SELECT -->
    <!-- ============================================== -->

    <label for="viewer_cpt">
        Select Content Type
    </label>

    <select name="viewer_cpt"
            id="viewer_cpt"
            onchange="this.form.submit()">

        <option value="">
            -- Select Content Type --
        </option>

        <?php foreach ($cpts as $pt => $label): ?>

            <option value="<?php echo esc_attr($pt); ?>"
                <?php selected($selected_cpt, $pt); ?>>

                <?php echo esc_html($label); ?>

            </option>

        <?php endforeach; ?>

    </select>

    <!-- ============================================== -->
    <!-- POST SELECT -->
    <!-- ============================================== -->

    <?php if ($selected_cpt): ?>

        <?php

        $posts = get_posts([
            'post_type'      => $selected_cpt,
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'post_status'    => 'publish',
            'fields'         => 'ids',
        ]);

        ?>

        <label for="viewer_post"
               style="margin-top:1rem; display:block;">

            Select Entry
        </label>

        <select name="viewer_post"
                id="viewer_post"
                onchange="this.form.submit()">

            <option value="">
                -- Select Entry --
            </option>

            <?php foreach ($posts as $post_id): ?>

                <option value="<?php echo intval($post_id); ?>"
                    <?php selected($selected_post_id, $post_id); ?>>

                    <?php echo esc_html(get_the_title($post_id)); ?>

                </option>

            <?php endforeach; ?>

        </select>

    <?php endif; ?>

</form>

<!-- ====================================================== -->
<!-- OUTPUT -->
<!-- ====================================================== -->

<?php if ($selected_post_id): ?>

<?php

$post = get_post($selected_post_id);

if ($post):

    /*
    |--------------------------------------------------------------------------
    | Placeholder ACF Fields
    |--------------------------------------------------------------------------
    |
    | Replace these with your actual field names later.
    |
    */

    $quote_text     = get_field('quote_plain_text', $selected_post_id);
    $lyric_text     = get_field('lyric_plain_text', $selected_post_id);
    $concept_text   = get_field('definition', $selected_post_id);
    $excerpt_text   = get_field('excerpt_plain_text', $selected_post_id);

    $source         = get_field('source', $selected_post_id);
    $author         = get_field('author', $selected_post_id);
    $context        = get_field('context', $selected_post_id);

    $post_type = get_post_type($selected_post_id);

?>

<div class="plaintext-output">

    <!-- ============================================== -->
    <!-- HEADER -->
    <!-- ============================================== -->

    <div class="plaintext-meta">

        <h3>
            <?php echo esc_html(get_the_title($selected_post_id)); ?>
        </h3>

        <p>

            <strong>Type:</strong>
            <?php echo esc_html($post_type); ?>

        </p>

        <?php if ($author): ?>

            <p>

                <strong>Author:</strong>
                <?php echo esc_html($author); ?>

            </p>

        <?php endif; ?>

        <?php if ($source): ?>

            <p>

                <strong>Source:</strong>
                <?php echo esc_html($source); ?>

            </p>

        <?php endif; ?>

    </div>

    <hr style="margin:2rem 0;">

    <!-- ============================================== -->
    <!-- CONTENT -->
    <!-- ============================================== -->

    <div class="plaintext-body">

        <?php

        /*
        |--------------------------------------------------------------------------
        | QUOTES
        |--------------------------------------------------------------------------
        */

        if ($post_type === 'quote'):

        ?>

            <blockquote class="plaintext-block">

                <?php
                echo nl2br(
                    esc_html($quote_text ?: '[quote_text field]')
                );
                ?>

            </blockquote>

        <?php

        /*
        |--------------------------------------------------------------------------
        | LYRICS
        |--------------------------------------------------------------------------
        */

        elseif ($post_type === 'lyric'):

        ?>

            <pre class="plaintext-pre">

<?php
echo esc_html($lyric_text ?: '[lyric_text field]');
?>

            </pre>

        <?php

        /*
        |--------------------------------------------------------------------------
        | CONCEPTS
        |--------------------------------------------------------------------------
        */

        elseif ($post_type === 'concept'):

        ?>

            <div class="plaintext-concept">

                <?php
                echo wpautop(
                    esc_html($concept_text ?: '[concept_text field]')
                );
                ?>

            </div>

        <?php

        /*
        |--------------------------------------------------------------------------
        | EXCERPTS
        |--------------------------------------------------------------------------
        */

        elseif ($post_type === 'excerpt'):

        ?>

            <div class="plaintext-excerpt">

                <?php
                echo wpautop(
                    esc_html($excerpt_text ?: '[excerpt_text field]')
                );
                ?>

            </div>

        <?php endif; ?>

    </div>

    <!-- ============================================== -->
    <!-- CONTEXT -->
    <!-- ============================================== -->

    <?php if ($context): ?>

        <hr style="margin:2rem 0;">

        <div class="plaintext-context">

            <h4>Context</h4>

            <?php echo wpautop(esc_html($context)); ?>

        </div>

    <?php endif; ?>

    <!-- ============================================== -->
    <!-- RAW WP CONTENT -->
    <!-- ============================================== -->

    <?php if (!empty($post->post_content)): ?>

        <hr style="margin:2rem 0;">

        <div class="plaintext-wordpress-content">

            <h4>WordPress Content</h4>

            <?php echo wpautop(esc_html($post->post_content)); ?>

        </div>

    <?php endif; ?>

</div>

<?php endif; ?>
<?php endif; ?>

</section>

<style>

.tool-plaintext-viewer {

    max-width: 1100px;
}

.plaintext-output {

    margin-top: 2rem;
}

.plaintext-body {

    font-size: 1.05rem;
    line-height: 1.8;
}

.plaintext-block {

    border-left: 4px solid #999;
    padding-left: 1.5rem;
    margin-left: 0;
    font-style: italic;
}

.plaintext-pre {

    white-space: pre-wrap;
    font-family: inherit;
    line-height: 1.8;
}

.plaintext-concept,
.plaintext-excerpt {

    max-width: 850px;
}

.plaintext-meta p {

    margin-bottom: 0.35rem;
}

</style>