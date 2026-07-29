<?php
// inc/footnotes/themes.php
// ===============================
// Themes Referenced
// ===============================

function fn_themes($chapter_id, $group_titles) {
    $themes = get_the_terms($chapter_id, 'theme');
    if (!$themes || is_wp_error($themes)) {
        return '';
    }

    usort($themes, fn($a, $b) => strcmp($a->name, $b->name));

    ob_start();
    $meta = $group_titles['theme'];

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