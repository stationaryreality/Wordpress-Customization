<?php

$post_types = [
    'artist',
    'profile',
    'book',
    'concept',
    'movie',
    'quote',
    'lyric',
    'organization',
    'image',
    'song',
    'chapter',
    'excerpt',
    'fragment',
    'element',
    'show',
    'game',
    'portal',
    'video'
];

$entries = [];
$type_counts = [];

$q = new WP_Query([
    'post_type'      => $post_types,
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'title',
    'order'          => 'ASC'
]);

while ($q->have_posts()) {

    $q->the_post();

    $post_id = get_the_ID();

    $references = get_field('references', $post_id);

    if (empty($references)) {
        continue;
    }

    $type = get_post_type();

    $meta = get_cpt_metadata($type);

    $entries[] = [
        'id'         => $post_id,
        'title'      => get_the_title(),
        'url'        => get_permalink(),
        'type'       => $type,
        'emoji'      => $meta['emoji'] ?? '•',
        'references' => $references,
    ];

    $type_counts[$type] = ($type_counts[$type] ?? 0) + 1;
}

wp_reset_postdata();

/* alphabetical */

usort($entries, function($a, $b) {
    return strcasecmp($a['title'], $b['title']);
});

$total_count = count($entries);

?>

<section class="tool-sources">

<header class="tool-header">

<h2>Source Index</h2>

<p>
<?php echo number_format($total_count); ?>
 content items contain source data.
</p>

</header>

<div class="cpt-filters" style="margin-bottom:2em;">

<button onclick="selectAllSources(true)">Select All</button>
<button onclick="selectAllSources(false)">Deselect All</button>

<div style="margin-top:1em; display:flex; flex-wrap:wrap; gap:12px;">

<?php foreach ($type_counts as $type => $count): ?>

<label>

<input
    type="checkbox"
    value="<?php echo esc_attr($type); ?>"
    checked>

<?php echo esc_html(ucfirst($type)); ?>
(<?php echo intval($count); ?>)

</label>

<?php endforeach; ?>

</div>

</div>

<?php foreach ($entries as $entry): ?>

<div
    class="source-entry"
    data-type="<?php echo esc_attr($entry['type']); ?>"
    style="
        margin-bottom:2rem;
        padding-bottom:2rem;
        border-bottom:1px solid #ddd;
    "
>

<h3 style="margin-bottom:0.5rem;">

<?php echo esc_html($entry['emoji']); ?>

<a href="<?php echo esc_url($entry['url']); ?>">
<?php echo esc_html($entry['title']); ?>
</a>

</h3>

<div style="margin-left:1.5rem;">

<?php
foreach ($entry['references'] as $ref) :

    $label = $ref['reference_label'] ?? '';
    $title = $ref['reference_title'] ?? '';
    $url   = $ref['reference_url'] ?? '';
    $note  = $ref['reference_note'] ?? '';   // ← new field
?>

<div style="margin-bottom:1.25rem;">

<?php if ($label) : ?>
<div><strong><?php echo esc_html($label); ?></strong></div>
<?php endif; ?>

<?php if ($title) : ?>
<div><?php echo esc_html($title); ?></div>
<?php endif; ?>

<?php if ($note) : ?>
<div><strong>Note:</strong> <?php echo esc_html($note); ?></div>
<?php endif; ?>

<?php if ($url) : ?>

<div>

<a
    href="<?php echo esc_url($url); ?>"
    target="_blank"
    rel="noopener noreferrer"
>

View Source

</a>

</div>

<?php endif; ?>

</div>

<?php endforeach; ?>

</div>

</div>

<?php endforeach; ?>

</section>

<script>

document.addEventListener('DOMContentLoaded', function() {

    const checkboxes =
        document.querySelectorAll('.cpt-filters input[type="checkbox"]');

    const entries =
        document.querySelectorAll('.source-entry');

    function filterSources() {

        const active =
            Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        entries.forEach(entry => {

            const type =
                entry.getAttribute('data-type');

            entry.style.display =
                active.includes(type)
                ? ''
                : 'none';
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', filterSources);
    });

    window.selectAllSources = function(state) {

        checkboxes.forEach(cb => {
            cb.checked = state;
        });

        filterSources();
    };

});

</script>