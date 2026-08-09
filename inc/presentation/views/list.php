<?php

$sections        = $knowledge_data['sections'] ?? [];
$section_order   = $knowledge_data['section_order'] ?? [];
$section_labels  = $knowledge_data['section_labels'] ?? [];
$map             = $knowledge_data['map'] ?? [];

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

        $label = $section_labels[$type] ?? ucfirst($type);

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
                        <?php 
                        // FIX: Chain fallback to master $map if entry icon is missing
                        echo esc_html($entry['icon'] ?? ($map[$entry['type']]['emoji'] ?? '•')); 
                        ?>
                    </span>

                    <a href="<?php echo esc_url($entry['url']); ?>">
                        <?php echo esc_html($entry['title']); ?>
                    </a>

                    <!-- REMOVED: Redundant portal-type-label span -->

                </li>

            <?php endforeach; ?>

        </ul>

    </section>

    <?php endforeach; ?>

</main>