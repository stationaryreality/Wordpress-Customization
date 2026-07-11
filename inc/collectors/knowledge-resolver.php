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


//temporary backwards compat - 2026-07-08

function kp_find_portal($name)
{
    $result = kp_resolve_knowledge($name);

    return ($result && $result['type'] === 'portal')
        ? $result['object']
        : null;
}

function kp_find_concept($name)
{
    $result = kp_resolve_knowledge($name);

    return ($result && $result['type'] === 'concept')
        ? $result['object']
        : null;
}

function kp_find_topic($name)
{
    $result = kp_resolve_knowledge($name);

    return ($result && $result['type'] === 'topic')
        ? $result['term']
        : null;
}

function kp_find_theme($name)
{
    $result = kp_resolve_knowledge($name);

    return ($result && $result['type'] === 'theme')
        ? $result['term']
        : null;
}

function kp_resolve_primary_destination($name)
{
    $result = kp_resolve_knowledge($name);

    if (!$result) {

        return [
            'type'   => null,
            'object' => null,
        ];

    }

    return [

        'type'   => $result['type'],

        'object' =>
            $result['object']
            ?? $result['term']
            ?? null,

    ];
}