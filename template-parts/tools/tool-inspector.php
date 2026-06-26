<?php

/*
|--------------------------------------------------------------------------
| ACF / Relationship Inspector
|--------------------------------------------------------------------------
|
| Optimized inspector:
| 1. First dropdown = CPT type only
| 2. Second dropdown loads ONLY posts for selected CPT
| 3. Prevents massive 2,000+ option rendering
|
*/

$cpts = [
    'concept'      => '🔎 Lexicon',
    'portal'       => '🚪 Portal Pages',
    'quote'        => '💬 Quote Library',
    'excerpt'      => '📖 Excerpts Library',
    'lyric'        => '🎼 Song Excerpts',
    'song'         => '🎵 Songs Featured',
    'image'        => '🖼 Images Gallery',
    'organization' => '🏢 Organizations',
    'book'         => '📚 Books Cited',
    'movie'        => '🎬 Movies Referenced',
    'artist'       => '🎤 Artists Featured',
    'profile'      => '👤 People Referenced',
    'chapter'      => '🧵 Narrative Threads',
    'fragment'     => '📜 Narrative Episodes',
    'element'      => '⚛️ Narrative Elements',
    'show'         => '📺 TV Shows Referenced',
    'game'         => '🎮 Video Games',
];

$selected_cpt = isset($_GET['inspector_cpt'])
    ? sanitize_text_field($_GET['inspector_cpt'])
    : '';

$selected_post_id = isset($_GET['inspector_post'])
    ? intval($_GET['inspector_post'])
    : 0;

?>

<section class="tool-acf-inspector">

<header class="tool-header">

    <h2>Relationship Inspector</h2>

    <p>
        Inspect ACF relationships, taxonomy assignments,
        metadata, and structural connections for all CPTs.
    </p>

</header>

<!-- ====================================================== -->
<!-- SELECTOR -->
<!-- ====================================================== -->

<form method="get" class="inspector-form">

    <input type="hidden" name="tool" value="inspector">

    <!-- ============================================== -->
    <!-- CPT SELECT -->
    <!-- ============================================== -->

    <label for="inspector_cpt">
        Select Content Type
    </label>

    <select name="inspector_cpt"
            id="inspector_cpt"
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

        <label for="inspector_post"
               style="margin-top:1rem; display:block;">

            Select Entry
        </label>

        <select name="inspector_post"
                id="inspector_post"
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

    $acf_fields = get_fields($selected_post_id);

?>

<div class="footnotes-viewer-output">

    <!-- ============================================== -->
    <!-- CORE INFO -->
    <!-- ============================================== -->

    <h3>
        <?php echo esc_html(get_the_title($selected_post_id)); ?>
    </h3>

    <p>
        <strong>Post Type:</strong>
        <?php echo esc_html(get_post_type($selected_post_id)); ?>
    </p>

    <p>
        <strong>Published:</strong>
        <?php echo esc_html(get_the_date('', $selected_post_id)); ?>
    </p>

    <p>
        <strong>Modified:</strong>
        <?php echo esc_html(get_the_modified_date('', $selected_post_id)); ?>
    </p>

    <p>
        <strong>Slug:</strong>
        <?php echo esc_html($post->post_name); ?>
    </p>

    <p>
        <strong>Menu Order:</strong>
        <?php echo intval($post->menu_order); ?>
    </p>

    <p>
        <strong>ID:</strong>
        <?php echo intval($selected_post_id); ?>
    </p>

    <p>
        <a href="<?php echo esc_url(get_permalink($selected_post_id)); ?>"
           target="_blank">

            View Original Entry →

        </a>
    </p>

    <hr style="margin:2rem 0;">

    <!-- ============================================== -->
    <!-- TAXONOMIES -->
    <!-- ============================================== -->

    <h3>Taxonomies</h3>

    <?php

    $taxonomies = ['theme', 'topic'];

    foreach ($taxonomies as $tax):

        $terms = get_the_terms($selected_post_id, $tax);

    ?>

        <h4><?php echo ucfirst($tax); ?></h4>

        <?php if ($terms && !is_wp_error($terms)): ?>

            <ul>

                <?php foreach ($terms as $term): ?>

                    <li>

                        <a href="<?php echo esc_url(get_term_link($term)); ?>">

                            <?php echo esc_html($term->name); ?>

                        </a>

                    </li>

                <?php endforeach; ?>

            </ul>

        <?php else: ?>

            <p>None</p>

        <?php endif; ?>

    <?php endforeach; ?>

    <hr style="margin:2rem 0;">

    <!-- ============================================== -->
    <!-- ACF FIELDS -->
    <!-- ============================================== -->

    <h3>ACF Fields</h3>

    <?php if ($acf_fields): ?>

        <?php foreach ($acf_fields as $field_name => $value): ?>

            <div style="margin-bottom:2rem;">

                <h4>
                    <?php echo esc_html($field_name); ?>
                </h4>

                <?php

                /*
                |--------------------------------------------------------------------------
                | Relationship Arrays
                |--------------------------------------------------------------------------
                */

                if (is_array($value)) {

                    echo '<ul>';

                    foreach ($value as $item) {

                        echo '<li>';

                        if ($item instanceof WP_Post) {

                            echo '<a href="' .
                                esc_url(get_permalink($item->ID)) .
                                '" target="_blank">' .
                                esc_html(get_the_title($item->ID)) .
                                '</a>';

                            echo ' <em>(' .
                                esc_html(get_post_type($item->ID)) .
                                ')</em>';
                        }

                        elseif (is_array($item)) {

                            echo '<pre>';
                            print_r($item);
                            echo '</pre>';
                        }

                        else {

                            echo esc_html(print_r($item, true));
                        }

                        echo '</li>';
                    }

                    echo '</ul>';
                }

                /*
                |--------------------------------------------------------------------------
                | Single Relationship
                |--------------------------------------------------------------------------
                */

                elseif ($value instanceof WP_Post) {

                    ?>

                    <p>

                        <a href="<?php echo esc_url(get_permalink($value->ID)); ?>"
                           target="_blank">

                            <?php echo esc_html(get_the_title($value->ID)); ?>

                        </a>

                        <em>
                            (<?php echo esc_html(get_post_type($value->ID)); ?>)
                        </em>

                    </p>

                    <?php

                }

                /*
                |--------------------------------------------------------------------------
                | Scalar Values
                |--------------------------------------------------------------------------
                */

                else {

                    ?>

                    <pre><?php echo esc_html(print_r($value, true)); ?></pre>

                    <?php
                }

                ?>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <p>No ACF fields found.</p>

    <?php endif; ?>

</div>

<?php endif; ?>
<?php endif; ?>

</section>