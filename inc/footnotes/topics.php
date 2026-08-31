<?php
// inc/footnotes/topics.php
// ===============================
// Topics Referenced
// ===============================

function fn_topics($chapter_id, $group_titles) {
    $taxonomy_context = kp_build_taxonomy_context($chapter_id);
    $topics = $taxonomy_context['topic'];
        if (empty($topics)) {
            return '';
        }

    usort($topics, fn($a, $b) => strcmp($a->name, $b->name));

    ob_start();
    $meta = $group_titles['topic'];

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