<?php

/*
|--------------------------------------------------------------------------
| CPT TYPES
|--------------------------------------------------------------------------
*/

$exclude = [
    'topic',
    'theme',
    'portal',
    'featured_artists',
    'other_artists',
    'songs_referenced'
];

$cpt_map = get_cpt_metadata();

$allowed_types = [];

foreach ($cpt_map as $key => $meta) {

    if (in_array($key, $exclude)) {
        continue;
    }

    $allowed_types[] = $key;
}

/*
|--------------------------------------------------------------------------
| TAXONOMY LISTS
|--------------------------------------------------------------------------
*/

$themes = get_terms([
    'taxonomy'   => 'theme',
    'hide_empty' => true,
    'orderby'    => 'count',
    'order'      => 'DESC',
    'number'     => 50
]);

$topics = get_terms([
    'taxonomy'   => 'topic',
    'hide_empty' => true,
    'orderby'    => 'count',
    'order'      => 'DESC',
    'number'     => 50
]);

/*
|--------------------------------------------------------------------------
| TERM RENDERER
|--------------------------------------------------------------------------
*/

function render_taxonomy_review_term($term, $taxonomy, $allowed_types) {

    $posts = get_posts([
        'post_type'      => $allowed_types,
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
        'tax_query'      => [
            [
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => $term->term_id
            ]
        ]
    ]);

    if (!$posts) {
        return;
    }

    $type_counts = [];

    foreach ($posts as $post) {

        $type = $post->post_type;

        if (!isset($type_counts[$type])) {
            $type_counts[$type] = 0;
        }

        $type_counts[$type]++;
    }

    ?>

    <details class="taxonomy-review">

        <summary>

            <?php echo esc_html($term->name); ?>

            <span style="opacity:.6;">
                (<?php echo number_format($term->count); ?>)
            </span>

        </summary>

        <div class="taxonomy-review-inner">

            <div class="taxonomy-breakdown">

                <?php foreach ($type_counts as $type => $count): ?>

                    <?php
                    $meta = get_cpt_metadata($type);
                    ?>

                    <span>

                        <?php echo $meta['emoji'] ?? '•'; ?>
                        <?php echo esc_html($type); ?>
                        (<?php echo $count; ?>)

                    </span>

                <?php endforeach; ?>

            </div>

            <p>

                <button
                    class="select-all"
                    data-term="<?php echo esc_attr($term->slug); ?>">
                    Select All
                </button>

                <button
                    class="clear-all"
                    data-term="<?php echo esc_attr($term->slug); ?>">
                    Clear
                </button>

            </p>

            <ul class="cpt-clean-list">

            <?php foreach ($posts as $post): ?>

                <?php

                $type = $post->post_type;

                $meta = get_cpt_metadata($type);

                ?>

                <li
                    data-taxonomy="<?php echo esc_attr($taxonomy); ?>"
                    data-term="<?php echo esc_attr($term->name); ?>"
                    data-id="<?php echo $post->ID; ?>"
                    data-type="<?php echo esc_attr($type); ?>"
                    data-group="<?php echo esc_attr($term->slug); ?>"
                >

                    <label>

                        <input
                            type="checkbox"
                            class="review-checkbox"
                        >

                        <span class="entry-emoji">
                            <?php echo $meta['emoji'] ?? '•'; ?>
                        </span>

                        <a
                            href="<?php echo get_permalink($post); ?>"
                            target="_blank"
                            rel="noopener"
                        >
                            <?php echo esc_html($post->post_title); ?>
                        </a>

                    </label>

                </li>

            <?php endforeach; ?>

            </ul>

        </div>

    </details>

    <?php
}

?>

<section class="admin-tool-section">

<h2>Portal Taxonomy Review</h2>

<p>
Review high-volume Topics and Themes and export IDs for taxonomy removal scripts.
</p>

<p>

<button onclick="generateRemovalExport()">
Generate Removal Export
</button>

</p>

<textarea
    id="removalExportOutput"
    style="width:100%;height:250px;"
></textarea>

<hr>

<h3>Top Themes</h3>

<?php
foreach ($themes as $theme) {
    render_taxonomy_review_term(
        $theme,
        'theme',
        $allowed_types
    );
}
?>

<hr>

<h3>Top Topics</h3>

<?php
foreach ($topics as $topic) {
    render_taxonomy_review_term(
        $topic,
        'topic',
        $allowed_types
    );
}
?>

</section>

<script>

    document.addEventListener('DOMContentLoaded', () => {

    /*
    |--------------------------------------------------------------------------
    | SELECT ALL
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.select-all').forEach(button => {

        button.addEventListener('click', () => {

            const slug = button.dataset.term;

            document
                .querySelectorAll(
                    `[data-group="${slug}"] .review-checkbox`
                )
                .forEach(cb => cb.checked = true);

        });

    });

    /*
    |--------------------------------------------------------------------------
    | CLEAR ALL
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.clear-all').forEach(button => {

        button.addEventListener('click', () => {

            const slug = button.dataset.term;

            document
                .querySelectorAll(
                    `[data-group="${slug}"] .review-checkbox`
                )
                .forEach(cb => cb.checked = false);

        });

    });

});

/*
|--------------------------------------------------------------------------
| EXPORT
|--------------------------------------------------------------------------
*/

window.generateRemovalExport = function() {

    const checked = document.querySelectorAll(
        '.review-checkbox:checked'
    );

    if (!checked.length) {
        return;
    }

    const grouped = {};

    checked.forEach(cb => {

        const li = cb.closest('li');

        const taxonomy = li.dataset.taxonomy;
        const term     = li.dataset.term;
        const type     = li.dataset.type;
        const id       = li.dataset.id;

        const key = taxonomy + '|' + term;

        if (!grouped[key]) {
            grouped[key] = {};
        }

        if (!grouped[key][type]) {
            grouped[key][type] = [];
        }

        grouped[key][type].push(id);

    });

    let output = '';

    Object.keys(grouped).forEach(group => {

        output += group + "\n\n";

        Object.keys(grouped[group])
            .sort()
            .forEach(type => {

                output +=
                    type +
                    ':' +
                    grouped[group][type].join(',');

                output += "\n";

            });

        output += "\n-----------------\n\n";

    });

    document.getElementById(
        'removalExportOutput'
    ).value = output;

};

</script>