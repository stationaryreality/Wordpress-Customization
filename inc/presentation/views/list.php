<?php

$sections        = $portal_data['sections'] ?? [];
$section_order   = $portal_data['section_order'] ?? [];
$section_labels  = $portal_data['section_labels'] ?? [];
$map             = $portal_data['map'] ?? [];

?>

<main class="portal-list">

    <header class="portal-list-header">

        <h1><?php the_title(); ?></h1>

        <?php if (has_excerpt()) : ?>

            <div class="portal-list-description">
                <?php the_excerpt(); ?>
            </div>

        <?php endif; ?>

    </header>

    <?php foreach ($section_order as $type) :

    $entries = $sections[$type] ?? [];

    if (empty($entries)) {
        continue;
    }

    $label =
        $section_labels[$type]
        ?? ucfirst($type);

?>

<section class="portal-list-section">

    <h2 class="portal-list-title">

        <?php echo esc_html($label); ?>

        (<?php echo count($entries); ?>)

    </h2>

    <ul class="portal-entry-list">

        <?php foreach ($entries as $entry) : ?>

            <li>

                <span class="portal-icon">

                    <?php echo esc_html($entry['icon']); ?>

                </span>

                <a href="<?php echo esc_url($entry['url']); ?>">

                    <?php echo esc_html($entry['title']); ?>

                </a>

                <span class="portal-type-label">

                    <?php

                    echo esc_html(
                        $map[$entry['type']]['title']
                        ?? ucfirst($entry['type'])
                    );

                    ?>

                </span>

            </li>

        <?php endforeach; ?>

    </ul>

</section>

<?php endforeach; ?>

</main>