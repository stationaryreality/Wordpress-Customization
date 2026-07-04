<?php

$sections        = $portal_data['sections'] ?? [];
$active_sections = $portal_data['active_sections'] ?? [];
$total_entries   = $portal_data['total_entries'] ?? 0;
$section_order   = $portal_data['section_order'] ?? [];
$section_labels  = $portal_data['section_labels'] ?? [];
$map             = $portal_data['map'] ?? [];

?>

<main class="portal-atlas">

    <div class="portal-shell">

        <!-- HERO -->

        <header class="portal-hero">

            <div class="portal-hero-inner">

                <p class="portal-kicker">
                    Knowledge Atlas
                </p>

                <h1 class="portal-title">
                    <?php the_title(); ?>
                </h1>

                <?php if (has_excerpt()) : ?>

                    <div class="portal-description">
                        <?php the_excerpt(); ?>
                    </div>

                <?php endif; ?>

                <div class="portal-meta-grid">

                    <div class="portal-meta-card">

                        <span class="portal-meta-number">
                            <?php echo esc_html($total_entries); ?>
                        </span>

                        <span class="portal-meta-label">
                            Related Entries
                        </span>

                    </div>

                    <div class="portal-meta-card">

                        <span class="portal-meta-number">
                            <?php echo esc_html(count($active_sections)); ?>
                        </span>

                        <span class="portal-meta-label">
                            Active Sections
                        </span>

                    </div>

                </div>

            </div>

        </header>

        <!-- NAV -->

        <?php if (!empty($active_sections)) : ?>

            <nav class="portal-section-nav">

                <?php foreach ($active_sections as $type => $count) : ?>

                    <a
                        href="#section-<?php echo esc_attr($type); ?>"
                        class="portal-nav-pill"
                    >

                        <span>
                            <?php
                            echo esc_html(
                                $section_labels[$type]
                                ?? ucfirst($type)
                            );
                            ?>
                        </span>

                        <strong>
                            <?php echo esc_html($count); ?>
                        </strong>

                    </a>

                <?php endforeach; ?>

            </nav>

        <?php endif; ?>

        <!-- SECTIONS -->

        <div class="portal-sections">

            <?php foreach ($section_order as $type) :

                $entries = $sections[$type];

                if (empty($entries)) {
                    continue;
                }

                $label = $section_labels[$type] ?? ucfirst($type);

            ?>

                <section
                    class="portal-section"
                    id="section-<?php echo esc_attr($type); ?>"
                >

                    <header class="portal-section-header">

                        <h2><?php echo esc_html($label); ?></h2>

                        <span class="portal-section-count">
                            <?php echo count($entries); ?>
                        </span>

                    </header>

                    <div class="portal-card-grid">

                        <?php foreach ($entries as $entry) : ?>

                            <article class="portal-card">

                                <a
                                    href="<?php echo esc_url($entry['url']); ?>"
                                    class="portal-card-inner"
                                >

                                    <?php if (!empty($entry['image'])) : ?>

                                        <div class="portal-card-image">

                                            <img
                                                src="<?php echo esc_url($entry['image']); ?>"
                                                alt="<?php echo esc_attr($entry['title']); ?>"
                                            >

                                        </div>

                                    <?php endif; ?>

                                    <div class="portal-card-content">

                                        <div class="portal-card-top">

                                            <span class="portal-card-icon">
                                                <?php echo esc_html($entry['icon']); ?>
                                            </span>

                                            <span class="portal-card-type">

                                                <?php
                                                echo esc_html(
                                                    $map[$entry['type']]['title']
                                                    ?? ucfirst($entry['type'])
                                                );
                                                ?>

                                            </span>

                                        </div>

                                        <h3 class="portal-card-title">
                                            <?php echo esc_html($entry['title']); ?>
                                        </h3>

                                        <?php if (!empty($entry['meta'])) : ?>

                                            <div class="portal-card-meta">
                                                <?php echo esc_html($entry['meta']); ?>
                                            </div>

                                        <?php endif; ?>

                                        <?php if (!empty($entry['excerpt'])) : ?>

                                            <div class="portal-card-excerpt">

                                                <?php
                                                echo wp_trim_words(
                                                    wp_strip_all_tags($entry['excerpt']),
                                                    40
                                                );
                                                ?>

                                            </div>

                                        <?php endif; ?>

                                    </div>

                                </a>

                            </article>

                        <?php endforeach; ?>

                    </div>

                </section>

</main>

            <?php endforeach; ?>

        </div>

    </div>

</main>