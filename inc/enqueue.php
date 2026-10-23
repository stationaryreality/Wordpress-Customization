<?php
/**
 * Enqueue styles and fonts for child theme.
 */

// Enqueue parent + child theme styles
function ct_author_child_enqueue_styles() {
    wp_enqueue_style(
        'author-parent-style',
        get_template_directory_uri() . '/style.css'
    );

}
add_action('wp_enqueue_scripts', 'ct_author_child_enqueue_styles');

// Disable Google Fonts from parent theme
function child_theme_remove_google_fonts() {
    wp_dequeue_style('author-fonts');
    wp_deregister_style('author-fonts');
}
add_action('wp_enqueue_scripts', 'child_theme_remove_google_fonts', 20);

// Enqueue custom fonts
function child_theme_enqueue_custom_fonts() {
    wp_enqueue_style(
        'child-custom-fonts',
        get_stylesheet_directory_uri() . '/fonts/fonts.css',
        [],
        null
    );
}
add_action('wp_enqueue_scripts', 'child_theme_enqueue_custom_fonts');


/**
 * Phased conditional CSS loader.
 *
 * Modes:
 * stable = old behavior, but excludes listening-room.css from global loading
 * pages  = pages/*.css conditional, cpt/*.css still global
 * full   = pages/*.css and cpt/*.css conditional
 * legacy = old behavior exactly
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('THEME_CSS_LOADER_MODE')) {
    // Start here. Do not jump straight to full.
    define('THEME_CSS_LOADER_MODE', 'stable');
}

function theme_css_paths() {
    return [
        'path' => untrailingslashit(get_stylesheet_directory()) . '/assets/css',
        'uri'  => untrailingslashit(get_stylesheet_directory_uri()) . '/assets/css',
    ];
}

function theme_css_enqueue_file($file, $handle) {
    if (!file_exists($file)) {
        return;
    }

    $paths    = theme_css_paths();
    $relative = str_replace($paths['path'], '', $file);

    wp_enqueue_style(
        $handle,
        $paths['uri'] . $relative,
        [],
        filemtime($file)
    );
}

function theme_css_enqueue_directory($relative_dir, $prefix, $exclude_slugs = []) {
    $paths = theme_css_paths();
    $dir   = trim($relative_dir, '/');
    $files = glob($paths['path'] . '/' . $dir . '/*.css');

    if (!is_array($files) || empty($files)) {
        return;
    }

    foreach ($files as $file) {
        $slug = basename($file, '.css');

        if (in_array($slug, $exclude_slugs, true)) {
            continue;
        }

        theme_css_enqueue_file(
            $file,
            'css-' . $prefix . '-' . $slug
        );
    }
}

function theme_css_load_shared_global() {
    theme_css_enqueue_directory('global', 'global');

    // Components are shared UI. For now, keep them global to avoid breakage.
    theme_css_enqueue_directory('components', 'component');
    theme_css_enqueue_directory('components/footnotes', 'footnote');
    theme_css_enqueue_directory('components/tools', 'tool');
    theme_css_enqueue_directory('components/portals', 'portal');

    // If you later add admin CSS back, this will safely pick it up.
    theme_css_enqueue_directory('admin', 'admin');
}

function theme_css_load_legacy($exclude_listening_room = false) {
    theme_css_load_shared_global();

    theme_css_enqueue_directory('cpt', 'cpt');

    $exclude_pages = [];

    if ($exclude_listening_room) {
        $exclude_pages = apply_filters(
            'theme_css_do_not_load_global_pages',
            ['listening-room']
        );
    }

    theme_css_enqueue_directory('pages', 'page', $exclude_pages);
}

function theme_css_load_pages_conditional() {
    $paths = theme_css_paths();

    $page_rules = [
        'home' => function () {
            return is_front_page()
                || is_page('home')
                || is_page_template(['template-home.php', 'home.php', 'front-page.php']);
        },

        'listening-room' => function () {
            return is_page('listening-room')
                || is_page_template([
                    'template-listening-room-full.php',
                    'template-listening-room.php',
                    'listening-room.php',
                ]);
        },

        'newest' => function () {
            return is_page('newest')
                || is_page_template(['template-newest.php', 'newest.php']);
        },

        'portals' => function () {
            return is_page('portals')
                || is_page_template(['template-portals.php', 'portals.php']);
        },

        'rap-music' => function () {
            return is_page('rap-music')
                || is_page_template(['template-rap-music.php', 'rap-music.php']);
        },

        'site-tools' => function () {
            return is_page('site-tools')
                || is_page_template(['template-site-tools.php', 'site-tools.php']);
        },

        'themes' => function () {
            return is_page('themes')
                || is_page_template(['template-themes.php', 'themes.php']);
        },

        'top-content' => function () {
            return is_page('top-content')
                || is_page_template(['template-top-content.php', 'top-content.php']);
        },

        'topics' => function () {
            return is_page('topics')
                || is_page_template(['template-topics.php', 'topics.php']);
        },
    ];

    $page_rules = apply_filters('theme_page_css_rules', $page_rules);

    $files = glob($paths['path'] . '/pages/*.css');

    if (!is_array($files) || empty($files)) {
        return;
    }

    foreach ($files as $file) {
        $slug = basename($file, '.css');
        $load = false;

        // Explicit rule first.
        if (isset($page_rules[$slug]) && is_callable($page_rules[$slug])) {
            $load = (bool) call_user_func($page_rules[$slug]);
        }

        // Fallback: page slug matches CSS filename.
        if (!$load && is_page($slug)) {
            $load = true;
        }

        // Fallback: common template naming patterns.
        if (!$load) {
            $possible_templates = [
                "template-{$slug}.php",
                "template-{$slug}-page.php",
                "page-{$slug}.php",
                "{$slug}.php",
            ];

            if (is_page_template($possible_templates)) {
                $load = true;
            }
        }

        // Fallback: taxonomy/category/tag slug matches CSS filename.
        if (!$load && (is_tax($slug) || is_category($slug) || is_tag($slug))) {
            $load = true;
        }

        $load = apply_filters('theme_should_load_page_css', $load, $slug, $file);

        if ($load) {
            theme_css_enqueue_file($file, 'css-page-' . $slug);
        }
    }
}

function theme_css_load_cpt_conditional() {
    $paths = theme_css_paths();

    /**
     * Some CSS filenames may not exactly match the post type.
     * Example: artist-heads.css is probably for the artist CPT.
     */
    $cpt_aliases = apply_filters('theme_cpt_css_aliases', [
        'artist-heads' => 'artist',
    ]);

    /**
     * Use this when a CPT stylesheet is needed on a regular page.
     *
     * Example:
     * $force['song'] = ['home', 'rap-music', 'top-content'];
     */
    $force_cpt_on_pages = apply_filters('theme_force_cpt_css_on_pages', []);

    $files = glob($paths['path'] . '/cpt/*.css');

    if (!is_array($files) || empty($files)) {
        return;
    }

    foreach ($files as $file) {
        $css_slug  = basename($file, '.css');
        $post_type = isset($cpt_aliases[$css_slug]) ? $cpt_aliases[$css_slug] : $css_slug;
        $load      = false;

        if (post_type_exists($post_type)) {
            // Single CPT page.
            if (is_singular($post_type)) {
                $load = true;
            }

            // CPT archive page.
            if (!$load && is_post_type_archive($post_type)) {
                $load = true;
            }

            // Taxonomies connected to this CPT.
            if (!$load && is_tax()) {
                $taxonomies = get_object_taxonomies($post_type, 'names');

                if (!empty($taxonomies) && is_tax($taxonomies)) {
                    $load = true;
                }
            }
        }

        // Force CPT CSS onto specific regular pages.
        if (!$load && isset($force_cpt_on_pages[$css_slug])) {
            $pages = (array) $force_cpt_on_pages[$css_slug];

            if (is_page($pages)) {
                $load = true;
            }
        }

        $load = apply_filters('theme_should_load_cpt_css', $load, $css_slug, $post_type, $file);

        if ($load) {
            theme_css_enqueue_file($file, 'css-cpt-' . $css_slug);
        }
    }
}

function theme_css_loader() {
    if (is_admin()) {
        return;
    }

    $mode = THEME_CSS_LOADER_MODE;

    // Emergency fallback: old behavior exactly.
    if ($mode === 'legacy') {
        theme_css_load_legacy(false);
        return;
    }

    // Recommended starting point:
    // old behavior, but listening-room.css is not loaded globally.
    if ($mode === 'stable') {
        theme_css_load_legacy(true);
        return;
    }

    // Phase 2:
    // page CSS conditional, CPT CSS still global.
    if ($mode === 'pages') {
        theme_css_load_shared_global();
        theme_css_enqueue_directory('cpt', 'cpt');
        theme_css_load_pages_conditional();
        return;
    }

    // Phase 3:
    // page CSS and CPT CSS conditional.
    if ($mode === 'full') {
        theme_css_load_shared_global();
        theme_css_load_pages_conditional();
        theme_css_load_cpt_conditional();
        return;
    }

    // If someone types an invalid mode, fail safely.
    theme_css_load_legacy(true);
}
add_action('wp_enqueue_scripts', 'theme_css_loader', 5);

/**
 * Optional debug output.
 *
 * Visit any page as an admin with:
 * /?css_debug=1
 */
function theme_css_loader_debug() {
    if (!isset($_GET['css_debug'])) {
        return;
    }

    if (!current_user_can('manage_options')) {
        return;
    }

    $styles = wp_styles();
    $lines  = [];

    foreach ((array) $styles->queue as $handle) {
        if (!isset($styles->registered[$handle])) {
            continue;
        }

        $src = $styles->registered[$handle]->src;

        if (strpos($src, '/assets/css/') !== false) {
            $lines[] = $handle . ' => ' . $src;
        }
    }

    if (empty($lines)) {
        return;
    }

    echo '<pre style="
        position: relative;
        z-index: 999999;
        background: #111;
        color: #7CFC00;
        padding: 20px;
        margin-top: 40px;
        font-size: 12px;
        line-height: 1.4;
        white-space: pre-wrap;
        word-break: break-word;
    ">';

    echo esc_html('Loaded CSS: ' . count($lines) . "\n\n");
    echo esc_html(implode("\n", $lines));

    echo '</pre>';
}
add_action('wp_footer', 'theme_css_loader_debug', 99);


// === KEYBOARD NAVIGATION SCRIPT ===
function enqueue_keyboard_navigation() {
    // Only load on single Image or Concept pages
        if (is_singular(['image', 'concept', 'artist', 'book', 'chapter', 'fragment', 'element', 'excerpt', 
        'game', 'movie', 'show', 'lyric', 'organization', 'profile', 'quote', 'song', 'video'])) {
            wp_enqueue_script(
            'keyboard-nav',
            get_stylesheet_directory_uri() . '/assets/js/keyboard-nav.js',
            [],
            filemtime(get_stylesheet_directory() . '/assets/js/keyboard-nav.js'),
            true // Load in footer
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_keyboard_navigation');