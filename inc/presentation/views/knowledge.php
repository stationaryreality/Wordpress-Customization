<?php

/**
 * Shared Knowledge View
 *
 * Receives:
 *
 * $sections
 */

if (empty($sections)) {
    return;
}

kp_render_knowledge_sections($sections);