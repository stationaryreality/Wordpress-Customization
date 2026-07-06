<?php
/**
 * ==========================================================
 * Knowledge Resolver
 * ----------------------------------------------------------
 * Centralized helpers for resolving Topics, Themes,
 * Concepts and Portals from a common name.
 *
 * Nothing in this file renders HTML.
 * It only returns objects.
 * ==========================================================
 */


/**
 * ----------------------------------------------------------
 * Portal
 * ----------------------------------------------------------
 */

function kp_find_portal($name) {

    $portal = get_page_by_title($name, OBJECT, 'portal');

    return $portal ?: null;
}


/**
 * ----------------------------------------------------------
 * Concept
 * ----------------------------------------------------------
 */

function kp_find_concept($name) {

    $concept = get_page_by_title($name, OBJECT, 'concept');

    return $concept ?: null;
}


/**
 * ----------------------------------------------------------
 * Topic
 * ----------------------------------------------------------
 */

function kp_find_topic($name) {

    $topic = get_term_by('name', $name, 'topic');

    return (!is_wp_error($topic) && $topic)
        ? $topic
        : null;
}


/**
 * ----------------------------------------------------------
 * Theme
 * ----------------------------------------------------------
 */

function kp_find_theme($name) {

    $theme = get_term_by('name', $name, 'theme');

    return (!is_wp_error($theme) && $theme)
        ? $theme
        : null;
}


/**
 * ----------------------------------------------------------
 * Resolve Primary Destination
 *
 * Priority:
 *
 * Portal
 * Concept
 * Topic
 * Theme
 *
 * Returns:
 *
 * [
 *     'type' => portal|concept|topic|theme|null,
 *     'object' => WP_Post|WP_Term|null
 * ]
 * ----------------------------------------------------------
 */

function kp_resolve_primary_destination($name) {

    if ($portal = kp_find_portal($name)) {

        return [
            'type'   => 'portal',
            'object' => $portal,
        ];
    }

    if ($concept = kp_find_concept($name)) {

        return [
            'type'   => 'concept',
            'object' => $concept,
        ];
    }

    if ($topic = kp_find_topic($name)) {

        return [
            'type'   => 'topic',
            'object' => $topic,
        ];
    }

    if ($theme = kp_find_theme($name)) {

        return [
            'type'   => 'theme',
            'object' => $theme,
        ];
    }

    return [
        'type'   => null,
        'object' => null,
    ];
}