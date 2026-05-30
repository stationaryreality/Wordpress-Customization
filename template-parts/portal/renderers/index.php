<?php

$sections = $portal_data['sections'] ?? [];
$map      = $portal_data['map'] ?? [];

/*
|--------------------------------------------------------------------------
| FLATTEN ENTRIES
|--------------------------------------------------------------------------
*/

$entries = [];
$type_counts = [];

foreach ($sections as $type => $section_entries) {

    foreach ($section_entries as $entry) {

        $entries[] = [

            'title' => $entry['title'],
            'url'   => $entry['url'],
            'icon'  => $entry['icon'] ?? '•',
            'type'  => $type,

        ];

        if (!isset($type_counts[$type])) {
            $type_counts[$type] = 0;
        }

        $type_counts[$type]++;
    }
}

/*
|--------------------------------------------------------------------------
| SORT
|--------------------------------------------------------------------------
*/

usort($entries, function ($a, $b) {

    return strcasecmp(
        $a['title'],
        $b['title']
    );

});

ksort($type_counts);

$total_count = count($entries);

/*
|--------------------------------------------------------------------------
| ALPHABET GROUPS
|--------------------------------------------------------------------------
*/

$groups = [];

foreach ($entries as $entry) {

    $letter = strtoupper(
        mb_substr($entry['title'], 0, 1)
    );

    if (!preg_match('/[A-Z]/', $letter)) {
        $letter = '#';
    }

    $groups[$letter][] = $entry;
}

ksort($groups);

?>

<section class="portal-index">

    <header class="portal-index-header">

        <h1><?php the_title(); ?></h1>

        <?php if (has_excerpt()) : ?>

            <div class="portal-index-description">
                <?php the_excerpt(); ?>
            </div>

        <?php endif; ?>

        <p class="portal-index-total">

            <?php echo number_format($total_count); ?>

            entries

        </p>

    </header>

    <!-- FILTERS -->

    <div class="portal-index-filters">

        <button onclick="portalSelectAll(true)">
            Select All
        </button>

        <button onclick="portalSelectAll(false)">
            Deselect All
        </button>

        <div class="portal-filter-list">

            <?php foreach ($type_counts as $type => $count) : ?>

                <label>

                    <input
                        type="checkbox"
                        value="<?php echo esc_attr($type); ?>"
                        checked
                    >

                    <?php

                    echo esc_html(
                        $map[$type]['title']
                        ?? ucfirst($type)
                    );

                    ?>

                    (<?php echo $count; ?>)

                </label>

            <?php endforeach; ?>

        </div>

    </div>

    <!-- A-Z NAV -->

    <nav class="portal-alpha-nav">

        <?php foreach ($groups as $letter => $items) : ?>

            <a href="#group-<?php echo esc_attr($letter); ?>">

                <?php echo esc_html($letter); ?>

            </a>

        <?php endforeach; ?>

    </nav>

    <!-- GROUPS -->

    <div class="portal-index-groups">

        <?php foreach ($groups as $letter => $items) : ?>

            <section
                id="group-<?php echo esc_attr($letter); ?>"
                class="portal-alpha-group"
            >

                <h2 class="portal-alpha-letter">

                    <?php echo esc_html($letter); ?>

                </h2>

                <ul class="portal-alpha-list">

                    <?php foreach ($items as $entry) : ?>

                        <li
                            data-type="<?php echo esc_attr($entry['type']); ?>"
                        >

                            <span class="portal-entry-icon">

                                <?php echo esc_html($entry['icon']); ?>

                            </span>

                            <a href="<?php echo esc_url($entry['url']); ?>">

                                <?php echo esc_html($entry['title']); ?>

                            </a>

                        </li>

                    <?php endforeach; ?>

                </ul>

            </section>

        <?php endforeach; ?>

    </div>

</section>

<script>

document.addEventListener('DOMContentLoaded', function() {

    const checkboxes =
        document.querySelectorAll(
            '.portal-index-filters input[type="checkbox"]'
        );

    const items =
        document.querySelectorAll(
            '.portal-alpha-list li'
        );

    function filterPortalIndex() {

        const active = Array.from(checkboxes)

            .filter(cb => cb.checked)

            .map(cb => cb.value);

        items.forEach(item => {

            const type =
                item.getAttribute('data-type');

            item.style.display =
                active.includes(type)
                    ? ''
                    : 'none';

        });

    }

    checkboxes.forEach(cb => {

        cb.addEventListener(
            'change',
            filterPortalIndex
        );

    });

    window.portalSelectAll = function(state) {

        checkboxes.forEach(cb => {

            cb.checked = state;

        });

        filterPortalIndex();

    };

});

</script>