<?php
// inc/footnotes/topics.php

function fn_topics($chapter_id, $group_titles) {
    $topics = get_the_terms($chapter_id, 'topic');
    if (!$topics || is_wp_error($topics)) return '';

    usort($topics, fn($a, $b) => strcmp($a->name, $b->name));

    ob_start();
    $meta = $group_titles['topic'];

    echo '<div class="referenced-group" style="margin-top:2em;">';
    echo '<h4><a href="'.esc_url($meta['link']).'" style="text-decoration:none;">
            <span style="font-size:1.1em;">'.esc_html($meta['emoji']).'</span>
            <span style="text-decoration:underline;">'.esc_html($meta['title']).'</span>
          </a></h4>';

    echo '<div class="tag-bubbles">';

    $count = count($topics);
    $i = 0;
foreach ($topics as $topic) {
    $link  = esc_url(get_term_link($topic));
    $title = esc_html($topic->name);
    
    echo "<span class=\"bubble-wrapper\"><a class=\"tag-bubble\" href=\"{$link}\">{$title}</a></span>\n";
}

    echo '</div></div>';
    return ob_get_clean();
}
