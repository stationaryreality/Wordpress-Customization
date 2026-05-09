<?php

function render_content_operations_page() {

    $search = $_GET['s'] ?? '';

    $post_types = [
        'concept',
        'portal',
        'quote',
        'excerpt',
        'lyric',
        'reference',
        'song',
        'image',
        'organization',
        'book',
        'movie',
        'artist',
        'profile',
        'chapter',
        'fragment',
        'element',
        'show',
        'game'
    ];

    /*
    |--------------------------------------------------------------------------
    | HANDLE BULK TAGGING
    |--------------------------------------------------------------------------
    */

    if (
        isset($_POST['content_ops_submit'])
        && !empty($_POST['selected_posts'])
        && !empty($_POST['taxonomy_name'])
        && !empty($_POST['taxonomy_value'])
    ) {

        $taxonomy = sanitize_text_field($_POST['taxonomy_name']);
        $term     = sanitize_text_field($_POST['taxonomy_value']);

        foreach ($_POST['selected_posts'] as $post_id) {

            wp_set_object_terms(
                intval($post_id),
                $term,
                $taxonomy,
                true
            );
        }

        echo '<div class="updated notice">';
        echo '<p>Bulk taxonomy assignment complete.</p>';
        echo '</div>';
    }

    ?>

    <div class="wrap">

        <h1>Content Operations</h1>

        <p>
            Search all CPTs and perform bulk taxonomy operations.
        </p>

        <!-- ====================================================== -->
        <!-- SEARCH -->
        <!-- ====================================================== -->

        <form method="get">

            <input type="hidden"
                   name="page"
                   value="content-operations">

            <input type="text"
                   name="s"
                   value="<?php echo esc_attr($search); ?>"
                   placeholder="Search titles and content..."
                   style="width:400px;">

            <button class="button button-primary">
                Search
            </button>

        </form>

        <hr>

        <?php

        if ($search):

            $q = new WP_Query([
                'post_type'      => $post_types,
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                's'              => $search,
                'orderby'        => 'title',
                'order'          => 'ASC'
            ]);

            if ($q->have_posts()):

        ?>

        <!-- ====================================================== -->
        <!-- BULK FORM -->
        <!-- ====================================================== -->

        <form method="post">

            <p>

                <button type="button"
                        class="button"
                        onclick="toggleAll(true)">

                    Select All

                </button>

                <button type="button"
                        class="button"
                        onclick="toggleAll(false)">

                    Deselect All

                </button>

            </p>

            <table class="widefat striped">

                <thead>

                    <tr>

                        <th></th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>ID</th>
                        <th>Themes</th>
                        <th>Topics</th>

                    </tr>

                </thead>

                <tbody>

                <?php while ($q->have_posts()): $q->the_post(); ?>

                    <?php

                    $themes = get_the_terms(get_the_ID(), 'theme');
                    $topics = get_the_terms(get_the_ID(), 'topic');

                    ?>

                    <tr>

                        <td>

                            <input type="checkbox"
                                   name="selected_posts[]"
                                   value="<?php echo get_the_ID(); ?>"
                                   checked>

                        </td>

                        <td>

                            <a href="<?php echo get_edit_post_link(); ?>">

                                <?php the_title(); ?>

                            </a>

                        </td>

                        <td>
                            <?php echo get_post_type(); ?>
                        </td>

                        <td>
                            <?php echo get_the_ID(); ?>
                        </td>

                        <td>

                            <?php

                            if ($themes && !is_wp_error($themes)) {

                                echo esc_html(
                                    implode(
                                        ', ',
                                        wp_list_pluck($themes, 'name')
                                    )
                                );
                            }

                            ?>

                        </td>

                        <td>

                            <?php

                            if ($topics && !is_wp_error($topics)) {

                                echo esc_html(
                                    implode(
                                        ', ',
                                        wp_list_pluck($topics, 'name')
                                    )
                                );
                            }

                            ?>

                        </td>

                    </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

            <hr>

            <!-- ====================================================== -->
            <!-- TAXONOMY ACTION -->
            <!-- ====================================================== -->

            <h2>Bulk Assign Taxonomy</h2>

            <p>

                <select name="taxonomy_name">

                    <option value="topic">
                        Topic
                    </option>

                    <option value="theme">
                        Theme
                    </option>

                </select>

                <input type="text"
                       name="taxonomy_value"
                       placeholder="earth">

                <button class="button button-primary"
                        name="content_ops_submit"
                        value="1">

                    Assign Taxonomy

                </button>

            </p>

        </form>

        <script>

        function toggleAll(state) {

            document.querySelectorAll(
                'input[name="selected_posts[]"]'
            ).forEach(el => {

                el.checked = state;

            });
        }

        </script>

        <?php

            else:

                echo '<p>No results found.</p>';

            endif;

            wp_reset_postdata();

        endif;

        ?>

    </div>

    <?php
}