<?php
// inc/footnotes/themes.php

function fn_themes($chapter_id, $group_titles) {
    $themes = get_the_terms($chapter_id, 'theme');
    if (!$themes || is_wp_error($themes)) return '';

    usort($themes, fn($a, $b) => strcmp($a->name, $b->name));

    ob_start();
    $meta = $group_titles['theme'];

    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo "<h4><a href=\"{$meta['link']}\" style=\"text-decoration:none;\">
            <span style=\"font-size:1.1em;\">{$meta['emoji']}</span> 
            <span style=\"text-decoration:underline;\">{$meta['title']}</span>
          </a></h4><ul>";

    foreach ($themes as $theme) {
        $link = get_term_link($theme);
        $title = esc_html($theme->name);
        echo "<li><a href=\"{$link}\"><strong>{$title}</strong></a></li>";
    }

    echo '</ul></div>';
    return ob_get_clean();
}
