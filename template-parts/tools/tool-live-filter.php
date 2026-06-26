<?php

/*
|--------------------------------------------------------------------------
| Live Content Explorer
|--------------------------------------------------------------------------
*/

$post_types = [
    'concept',
    'portal',
    'quote',
    'excerpt',
    'lyric',
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

$map = get_cpt_metadata();

$entries = [];

$q = new WP_Query([
    'post_type'      => $post_types,
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'title',
    'order'          => 'ASC'
]);

while ($q->have_posts()) {

    $q->the_post();

    $type = get_post_type();

    $entries[] = [
        'id'    => get_the_ID(),
        'title' => get_the_title(),
        'url'   => get_permalink(),
        'type'  => $type,
        'emoji' => $map[$type]['emoji'] ?? '•'
    ];
}

wp_reset_postdata();

?>

<section class="tool-live-search">

<header class="tool-header">

    <h2>Live Content Explorer</h2>

    <p>
        Instantly filter and explore all published content
        across the site.
    </p>

</header>

<!-- ====================================================== -->
<!-- SEARCH -->
<!-- ====================================================== -->

<div class="live-search-box">

    <input type="text"
           id="liveSearch"
           placeholder="Type to explore content...">

</div>

<!-- ====================================================== -->
<!-- RESULTS -->
<!-- ====================================================== -->

<div class="live-search-results">

    <ul id="liveSearchList">

        <?php foreach ($entries as $e): ?>

            <li
                data-title="<?php echo esc_attr(strtolower($e['title'])); ?>"
                data-type="<?php echo esc_attr($e['type']); ?>"
                data-id="<?php echo intval($e['id']); ?>"
            >

                <span class="entry-emoji">
                    <?php echo esc_html($e['emoji']); ?>
                </span>

                <a href="<?php echo esc_url($e['url']); ?>"
                   target="_blank">

                    <?php echo esc_html($e['title']); ?>

                </a>

                <small>
                    (<?php echo esc_html($e['type']); ?>)
                </small>

            </li>

        <?php endforeach; ?>

    </ul>

</div>

<!-- ====================================================== -->
<!-- EXPORT -->
<!-- ====================================================== -->

<div class="live-search-export">

    <button type="button"
            onclick="generateIdExport()">

        Generate ID Export

    </button>

    <textarea
        id="idExportOutput"
        readonly
        placeholder="Grouped CPT IDs will appear here..."
    ></textarea>

</div>

</section>

<!-- ====================================================== -->
<!-- JS -->
<!-- ====================================================== -->

<script>

document.addEventListener('DOMContentLoaded', function() {

    const input = document.getElementById('liveSearch');
    const items = document.querySelectorAll('#liveSearchList li');

    /*
    |--------------------------------------------------------------------------
    | LIVE FILTER
    |--------------------------------------------------------------------------
    */

    input.addEventListener('input', function() {

        const q = this.value.toLowerCase().trim();

        items.forEach(item => {

            const title = item.dataset.title;

            if (title.includes(q)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }

        });

    });

    /*
    |--------------------------------------------------------------------------
    | GENERATE ID EXPORT
    |--------------------------------------------------------------------------
    */

    window.generateIdExport = function() {

        const visible = Array.from(items).filter(item => {
            return item.style.display !== 'none';
        });

        const grouped = {};

        visible.forEach(item => {

            const type = item.dataset.type;
            const id   = item.dataset.id;

            if (!grouped[type]) {
                grouped[type] = [];
            }

            grouped[type].push(id);

        });

        let output = '';

        Object.keys(grouped).sort().forEach(type => {

            output += type + ': ';
            output += grouped[type].join(',');
            output += "\n\n";

        });

        document.getElementById('idExportOutput').value = output;

    };

});

</script>