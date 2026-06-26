<?php

/*
|--------------------------------------------------------------------------
| Live Content Search
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

$search = isset($_GET['q'])
    ? sanitize_text_field($_GET['q'])
    : '';

$results = [];

if (!empty($search)) {

    $q = new WP_Query([
        'post_type'      => $post_types,
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        's'              => $search,
        'relevanssi'     => true,
    ]);

    /*
    |--------------------------------------------------------------------------
    | Relevanssi
    |--------------------------------------------------------------------------
    */

    if (function_exists('relevanssi_do_query')) {
        relevanssi_do_query($q);
    }

    while ($q->have_posts()) {

        $q->the_post();

        $type = get_post_type();

        $results[] = [
            'id'    => get_the_ID(),
            'title' => get_the_title(),
            'url'   => get_permalink(),
            'type'  => $type,
            'emoji' => $map[$type]['emoji'] ?? '•'
        ];
    }

    wp_reset_postdata();
}

?>

<section class="tool-live-search">

<header class="tool-header">

    <h2>Plain Content Search</h2>

    <p>
        Search titles and content across all published CPTs.
    </p>

</header>

<!-- ====================================================== -->
<!-- SEARCH -->
<!-- ====================================================== -->

<form method="get" class="live-search-box">

    <input
        type="hidden"
        name="tool"
        value="live-search"
    >

    <input
        type="text"
        name="q"
        value="<?php echo esc_attr($search); ?>"
        placeholder="Search content..."
    >

    <button type="submit">
        Search
    </button>

</form>

<!-- ====================================================== -->
<!-- RESULTS -->
<!-- ====================================================== -->

<?php if (!empty($search)): ?>

<div class="live-search-results">

    <p style="margin-bottom:1rem;">
        <?php echo count($results); ?> results found
    </p>

    <ul id="liveSearchList">

        <?php foreach ($results as $e): ?>

            <li
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

<?php endif; ?>

</section>

<!-- ====================================================== -->
<!-- JS -->
<!-- ====================================================== -->

<script>

function generateIdExport() {

    const items = document.querySelectorAll('#liveSearchList li');

    const grouped = {};

    items.forEach(item => {

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

}

</script>