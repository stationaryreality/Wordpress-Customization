<?php
/* Template Name: Site Tools */

$tool = $_GET['tool'] ?? 'index';

$allowed_tools = [
  'chapters-by-song',
  'content-density',
  'footnotes',
  'index',
  'inspector',
  'live-filter',
  'live-search',
  'media-library',
  'newest',
  'orphans',
  'plaintext',
  'sources',
  'tag-audit',
];

if (!in_array($tool, $allowed_tools)) {
  $tool = 'newest';
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>

<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php wp_head(); ?>

<style>

html,
body {
    margin: 0;
    padding: 0;
    background: #f5f5f5;
    font-family: system-ui, sans-serif;
}


body.site-tools-app::before {
    content: none !important;
    display: none !important;
}

/* ===== APP LAYOUT ===== */

.site-tools-shell {
    display: grid;
    grid-template-columns: 280px 1fr;
    min-height: 100vh;
}

/* ===== SIDEBAR ===== */

.site-tools-sidebar {
    background: #ffffff;
    border-right: 1px solid #ddd;
    padding: 1.5rem;
    overflow-y: auto;
}

.site-tools-logo {
    margin-bottom: 2rem;
}

.site-tools-logo a {
    text-decoration: none;
    color: #111;
    font-size: 1.25rem;
    font-weight: 700;
}

.site-tools-home {
    margin-top: 0.5rem;
    font-size: 0.9rem;
}

.site-tools-home a {
    color: #666;
    text-decoration: none;
}

/* ===== NAV ===== */

.admin-tools-nav {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.admin-tools-nav a {
    display: flex;
    align-items: flex-start;

    padding: 0.45rem 0.6rem;
    border-radius: 8px;

    text-decoration: none;
    color: #222;

    font-size: 0.95rem;
    line-height: 1.35;

    transition: background 0.15s ease;
}

.admin-tools-nav a::before {
    content: "▸";
    width: 18px;
    opacity: 0.6;
    flex-shrink: 0;
}

.admin-tools-nav a:hover {
    background: #f3f3f3;
}

.admin-tools-nav a.active {
    background: #111;
    color: #fff;
    font-weight: 600;
}

/* ===== CONTENT ===== */

.site-tools-content {
    padding: 2rem 3rem;
    width: 100%;
    min-width: 0;
}

.site-tools-content-inner {
    width: 100%;
    min-width: 0;
}

/* ===== MOBILE ===== */

@media (max-width: 900px) {

    .site-tools-shell {
        grid-template-columns: 1fr;
    }

    .site-tools-sidebar {
        border-right: 0;
        border-bottom: 1px solid #ddd;
    }

    .site-tools-content {
        padding: 1.5rem;
    }
}

</style>

</head>

<body <?php body_class('site-tools-app'); ?>>

<div class="site-tools-shell">

    <!-- SIDEBAR -->

    <aside class="site-tools-sidebar">

        <div class="site-tools-logo">

            <a href="/" target="_blank">
                Site Tools
            </a>

            <div class="site-tools-home">
                <a href="/" target="_blank">
                    ← Main Site
                </a>
            </div>

        </div>

        <nav class="admin-tools-nav">

            <a href="?tool=chapters-by-song"
               class="<?php echo $tool === 'chapters-by-song' ? 'active' : ''; ?>">
                Chapters by Song
            </a>

            <a href="?tool=content-density"
               class="<?php echo $tool === 'content-density' ? 'active' : ''; ?>">
                Content Density
            </a>

            <a href="?tool=footnotes"
               class="<?php echo $tool === 'footnotes' ? 'active' : ''; ?>">
                Footnotes Viewer
            </a>

            <a href="?tool=index"
               class="<?php echo $tool === 'index' ? 'active' : ''; ?>">
                Full Site Index
            </a>

            <a href="?tool=inspector"
               class="<?php echo $tool === 'inspector' ? 'active' : ''; ?>">
                Inspect Relationships
            </a>

            <a href="?tool=live-filter"
               class="<?php echo $tool === 'live-filter' ? 'active' : ''; ?>">
                Live Content Filter
            </a>

            <a href="?tool=live-search"
               class="<?php echo $tool === 'live-search' ? 'active' : ''; ?>">
                Live Content Search
            </a>

            <a href="?tool=media-library"
               class="<?php echo $tool === 'media-library' ? 'active' : ''; ?>">
                Media Library
            </a>

            <a href="?tool=newest"
               class="<?php echo $tool === 'newest' ? 'active' : ''; ?>">
                Newest Content
            </a>

            <a href="?tool=orphans"
               class="<?php echo $tool === 'orphans' ? 'active' : ''; ?>">
                Orphaned CPTs
            </a>

                <a href="?tool=plaintext"
               class="<?php echo $tool === 'plaintext' ? 'active' : ''; ?>">
                Plain Text Viewer
            </a>

                <a href="?tool=sources"
               class="<?php echo $tool === 'plaintext' ? 'active' : ''; ?>">
                Sources
            </a>

            <a href="?tool=tag-audit"
               class="<?php echo $tool === 'tag-audit' ? 'active' : ''; ?>">
                Tag Audit
            </a>

            <hr>



            <a href="/portals/" target="_blank">
                Portals
            </a>
                                    <a href="/developer-notes/" target="_blank">
                Site Development
            </a>

            <a href="/themes/" target="_blank">
                Themes
            </a>

            <a href="/topics/" target="_blank">
                Topics
            </a>

            <a href="/sitemap_index.xml" target="_blank">
                XML Sitemap
            </a>

        </nav>

    </aside>

    <!-- CONTENT -->

    <main class="site-tools-content">

        <div class="site-tools-content-inner">

        <?php

        /*
        |--------------------------------------------------------------------------
        | Tool Loader
        |--------------------------------------------------------------------------
        */

        if ($tool === 'chapters-by-song') {

          get_template_part('template-parts/tools/tool', 'chapters-by-song');

        }

        elseif ($tool === 'content-density') {

          get_template_part('template-parts/tools/tool', 'content-density');

        }

        elseif ($tool === 'footnotes') {

          get_template_part('template-parts/tools/tool', 'footnotes');

        }

        elseif ($tool === 'index') {

          get_template_part('template-parts/tools/tool', 'index');

        }

        elseif ($tool === 'inspector') {

          get_template_part('template-parts/tools/tool', 'inspector');

        }

        elseif ($tool === 'live-filter') {

          get_template_part('template-parts/tools/tool', 'live-filter');

        }

        elseif ($tool === 'live-search') {

          get_template_part('template-parts/tools/tool', 'live-search');

        }

        elseif ($tool === 'media-library') {

          get_template_part('template-parts/tools/tool', 'media-library');

        }

        elseif ($tool === 'newest') {

          get_template_part('template-parts/tools/tool', 'newest');

        }

        elseif ($tool === 'orphans') {

          get_template_part('template-parts/tools/tool', 'orphans');

        }

                elseif ($tool === 'plaintext') {

          get_template_part('template-parts/tools/tool', 'plaintext');

        }

         elseif ($tool === 'sources') {

          get_template_part('template-parts/tools/tool', 'sources');

        }

        elseif ($tool === 'tag-audit') {

          get_template_part('template-parts/tools/tool', 'tag-audit');

        }

        ?>

        </div>

    </main>

</div>

<?php wp_footer(); ?>

</body>
</html>
