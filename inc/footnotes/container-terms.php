<?php
// inc/footnotes/container-terms.php
// ===================================
// Universal Inherited Term Collector & Element Renderers
// ===================================

/**
 * Safely flattens the grouped reference context into a simple array of Post IDs.
 */
function kp_flatten_context_array($context) {
    $ids = [];
    if (empty($context)) return $ids;
    
    if (is_numeric($context)) return [(int)$context];
    if ($context instanceof WP_Post) return [$context->ID];
    if (is_object($context) && isset($context->ID)) return [(int)$context->ID];

    if (is_array($context)) {
        // If it's a single post array (e.g., ['ID' => 123])
        if (isset($context['ID'])) return [(int)$context['ID']];
        
        // Recursively flatten grouped arrays
        foreach ($context as $item) {
            $ids = array_merge($ids, kp_flatten_context_array($item));
        }
    }
    return $ids;
}

/**
 * The Master Collector: Gets inherited terms for Elements, Chapters, and Fragments.
 */
function kp_get_container_inherited_terms($post_id, $taxonomy) {
    $post_id   = (int) $post_id;
    $taxonomy  = sanitize_key($taxonomy);
    $post_type = get_post_type($post_id);
    $terms     = [];

    if (!$post_id || !$taxonomy || !$post_type) return $terms;

    $exclude_types = ['chapter', 'fragment', 'element'];
    $related_ids   = [];

    // --- 1. GATHER RELATED CPT IDs BASED ON CONTAINER TYPE ---
    
    if ($post_type === 'element') {
        // Elements use the simple ACF field
        $related = get_field('related_content', $post_id);
        if (!empty($related) && is_array($related)) {
            foreach ($related as $item) {
                if ($item instanceof WP_Post) $related_ids[] = $item->ID;
                elseif (is_numeric($item)) $related_ids[] = (int)$item;
            }
        }
        // Elements CAN have their own manual tags, so we include them
        $own_terms = get_the_terms($post_id, $taxonomy);
        if (!empty($own_terms) && !is_wp_error($own_terms)) {
            foreach ($own_terms as $term) $terms[$term->term_id] = $term;
        }
    } 
    elseif (in_array($post_type, ['chapter', 'fragment'])) {
        // Chapters/Fragments use the centralized context builder
        if (function_exists('kp_build_reference_context')) {
            $context = kp_build_reference_context($post_id);
            $related_ids = kp_flatten_context_array($context);
        }
        // Chapters/Fragments DO NOT get their own manual tags (as requested)
    }

    // --- 2. FETCH TERMS FROM ALL RELATED CPTS ---
    
    $related_ids = array_unique(array_filter($related_ids));
    
    foreach ($related_ids as $rel_id) {
        $rel_type = get_post_type($rel_id);
        
        // Skip containers, we only want leaf CPTs (lyrics, quotes, etc.)
        if (!$rel_type || in_array($rel_type, $exclude_types, true)) continue;

        $rel_terms = get_the_terms($rel_id, $taxonomy);
        if (!empty($rel_terms) && !is_wp_error($rel_terms)) {
            foreach ($rel_terms as $term) {
                // Deduplicate by term ID
                $terms[$term->term_id] = $term;
            }
        }
    }

    // --- 3. SORT AND RETURN ---
    
    if (!empty($terms)) {
        $terms = array_values($terms);
        usort($terms, fn($a, $b) => strcasecmp($a->name, $b->name));
    }

    return $terms;
}

/**
 * Renders Element Topics bubbles.
 */
function fn_element_topics($element_id, $group_titles) {
    $topics = kp_get_container_inherited_terms($element_id, 'topic');
    if (empty($topics)) return '';

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
    $themes = kp_get_container_inherited_terms($element_id, 'theme');
    if (empty($themes)) return '';

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