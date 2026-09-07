<?php
// inc/footnotes/element-terms.php
// ===================================
// Element Term Inheritance & Display
// ===================================

/**
 * Collects Topic/Theme terms for an Element.
 * Includes the Element's own terms + terms from all related CPTs.
 */
function kp_get_element_display_terms($element_id, $taxonomy) {
    $element_id = (int) $element_id;
    $taxonomy   = sanitize_key($taxonomy);
    $terms      = [];

    if (!$element_id || !$taxonomy) {
        return $terms;
    }

    // 1. Element's own terms (if any are assigned directly).
    $own_terms = get_the_terms($element_id, $taxonomy);
    if (!empty($own_terms) && !is_wp_error($own_terms)) {
        foreach ($own_terms as $term) {
            $terms[$term->term_id] = $term;
        }
    }

    // 2. Terms from related_content CPTs.
    $related = get_field('related_content', $element_id);
    if (empty($related) || !is_array($related)) {
        // Return own terms if no related content
        return $terms; 
    }

    $exclude_types = ['chapter', 'fragment', 'element'];

    foreach ($related as $item) {
        // We know it's a WP_Post object based on your setup.
        if (!($item instanceof WP_Post)) {
            continue;
        }

        // Skip containers.
        if (in_array($item->post_type, $exclude_types, true)) {
            continue;
        }

        $item_terms = get_the_terms($item->ID, $taxonomy);
        if (!empty($item_terms) && !is_wp_error($item_terms)) {
            foreach ($item_terms as $term) {
                // Deduplicate by term ID
                $terms[$term->term_id] = $term;
            }
        }
    }

    // Sort alphabetically by name.
    if (!empty($terms)) {
        usort($terms, fn($a, $b) => strcasecmp($a->name, $b->name));
    }

    return $terms;
}

/**
 * Renders Element Topics bubbles.
 */
function fn_element_topics($element_id, $group_titles) {
    $topics = kp_get_element_display_terms($element_id, 'topic');
    if (empty($topics)) {
        return '';
    }

    ob_start();
    $meta = $group_titles['topic'] ?? ['emoji' => '🧩', 'title' => 'Topics', 'link' => '/topics/'];

    echo '<div class="cpt-topic-footnote-group">';
    echo '<h4 class="cpt-topic-footnote-title">';
    echo '<a href="' . esc_url($meta['link']) . '">';
    echo '<span>' . esc_html($meta['emoji']) . '</span> ';
    echo '<span>' . esc_html($meta['title']) . '</span>';
    echo '</a>';
    echo '</h4>';
    echo '<div class="cpt-topic-footnote-bubbles">';

    foreach ($topics as $topic) {
        $link  = esc_url(get_term_link($topic));
        $title = esc_html($topic->name);
        echo '<span class="bubble-wrapper">';
        echo '<a class="cpt-topic-footnote-bubble" href="' . $link . '">' . $title . '</a>';
        echo "</span>\n";
    }

    echo '</div>';
    echo '</div>';

    return ob_get_clean();
}

/**
 * Renders Element Themes bubbles.
 */
function fn_element_themes($element_id, $group_titles) {
    $themes = kp_get_element_display_terms($element_id, 'theme');
    if (empty($themes)) {
        return '';
    }

    ob_start();
    $meta = $group_titles['theme'] ?? ['emoji' => '🎨', 'title' => 'Themes', 'link' => '/themes/'];

    echo '<div class="cpt-theme-footnote-group">';
    echo '<h4 class="cpt-theme-footnote-title">';
    echo '<a href="' . esc_url($meta['link']) . '">';
    echo '<span>' . esc_html($meta['emoji']) . '</span> ';
    echo '<span>' . esc_html($meta['title']) . '</span>';
    echo '</a>';
    echo '</h4>';
    echo '<div class="cpt-theme-footnote-bubbles">';

    foreach ($themes as $theme) {
        $link  = esc_url(get_term_link($theme));
        $title = esc_html($theme->name);
        echo '<span class="bubble-wrapper">';
        echo '<a class="cpt-theme-footnote-bubble" href="' . $link . '">' . $title . '</a>';
        echo "</span>\n";
    }

    echo '</div>';
    echo '</div>';

    return ob_get_clean();
}