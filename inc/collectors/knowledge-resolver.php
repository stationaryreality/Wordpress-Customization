 <?php
/**
 * Resolve a knowledge object from a name.
 *
 * Priority:
 *
 * Portal
 * Concept
 * Topic
 * Theme
 */

function kp_resolve_knowledge(string $name) {

    $name = trim($name);

    /*
    -----------------------------
    Portal
    -----------------------------
    */

    $portal = get_page_by_title($name, OBJECT, 'portal');

    if ($portal) {

        return [
            'type'   => 'portal',
            'object' => $portal,
        ];

    }

    /*
    -----------------------------
    Concept
    -----------------------------
    */

    $concept = get_page_by_title($name, OBJECT, 'concept');

    if ($concept) {

        return [
            'type'   => 'concept',
            'object' => $concept,
        ];

    }

    /*
    -----------------------------
    Topic
    -----------------------------
    */

    $topic = get_term_by('name', $name, 'topic');

    if ($topic) {

        return [
            'type' => 'topic',
            'term' => $topic,
        ];

    }

    /*
    -----------------------------
    Theme
    -----------------------------
    */

    $theme = get_term_by('name', $name, 'theme');

    if ($theme) {

        return [
            'type' => 'theme',
            'term' => $theme,
        ];

    }

    return null;

}