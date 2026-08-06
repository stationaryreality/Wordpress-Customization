<?php
/**
 * Atlas Portal View (Redesigned)
 * A clean, visual map of all CPTs.
 */

$sections        = $knowledge_data['sections'] ?? [];
$active_sections = $knowledge_data['active_sections'] ?? [];
$total_entries   = $knowledge_data['total_entries'] ?? 0;
$section_order   = $knowledge_data['section_order'] ?? [];
$section_labels  = $knowledge_data['section_labels'] ?? [];
$map             = $knowledge_data['map'] ?? [];

?>

<div class="portal-atlas">

    <!-- HERO -->
    <header class="atlas-hero">
        <span class="atlas-kicker">Knowledge Atlas</span>
        <h1 class="atlas-title"><?php the_title(); ?></h1>
        
        <?php if (has_excerpt()) : ?>
            <div class="atlas-description">
                <?php the_excerpt(); ?>
            </div>
        <?php endif; ?>

        <div class="atlas-stats">
            <div class="atlas-stat">
                <strong><?php echo esc_html($total_entries); ?></strong>
                <span>Total Entries</span>
            </div>
            <div class="atlas-stat">
                <strong><?php echo esc_html(count($active_sections)); ?></strong>
                <span>Active Sections</span>
            </div>
        </div>
    </header>

    <!-- NAVIGATION PILLS -->
    <?php if (!empty($active_sections)) : ?>
        <nav class="atlas-nav">
            <?php foreach ($active_sections as $type => $count) : ?>
                <a href="#section-<?php echo esc_attr($type); ?>" class="atlas-pill">
                    <?php echo esc_html($section_labels[$type] ?? ucfirst($type)); ?>
                    <span class="atlas-pill-count"><?php echo esc_html($count); ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>

    <!-- SECTIONS -->
    <div class="atlas-sections">
        <?php foreach ($section_order as $type) : 
            $entries = $sections[$type] ?? [];
            if (empty($entries)) continue;
            $label = $section_labels[$type] ?? ucfirst($type);
        ?>
            <section class="atlas-section" id="section-<?php echo esc_attr($type); ?>">
                <header class="atlas-section-header">
                    <h2><?php echo esc_html($label); ?></h2>
                    <span class="atlas-section-count"><?php echo count($entries); ?></span>
                </header>

                <div class="atlas-grid">
                    <?php foreach ($entries as $entry) : ?>
                        <article class="atlas-card">
                            <a href="<?php echo esc_url($entry['url']); ?>" class="atlas-card-link">
                                
                                <!-- Image / Icon Fallback -->
                                <div class="atlas-card-media">
                                    <?php if (!empty($entry['image'])) : ?>
                                        <img src="<?php echo esc_url($entry['image']); ?>" alt="<?php echo esc_attr($entry['title']); ?>" loading="lazy">
                                    <?php else : ?>
                                        <div class="atlas-card-icon-fallback">
                                            <?php echo esc_html($entry['icon'] ?? '•'); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Content -->
                                <div class="atlas-card-body">
                                    <span class="atlas-card-type">
                                        <?php echo esc_html($map[$entry['type']]['title'] ?? ucfirst($entry['type'])); ?>
                                    </span>
                                    <h3 class="atlas-card-title"><?php echo esc_html($entry['title']); ?></h3>
                                    
                                    <?php if (!empty($entry['excerpt'])) : ?>
                                        <p class="atlas-card-excerpt">
                                            <?php echo wp_trim_words(wp_strip_all_tags($entry['excerpt']), 20); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>

</div>