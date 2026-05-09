<?php
/* Template Name: Admin Tools */
get_header();

/*
|--------------------------------------------------------------------------
| Tool Routing
|--------------------------------------------------------------------------
|
| Default tool is now "newest"
|
*/

$tool = $_GET['tool'] ?? 'newest';

$allowed_tools = [
  'chapters-by-song',
  'footnotes',
  'index',
  'inspector',
  'live-filter',
  'live-search',
  'media-library',
  'newest',
  'orphans',
  'tag-audit',
];

if (!in_array($tool, $allowed_tools)) {
  $tool = 'newest';
}
?>

<main class="cpt-index-clean">

<header class="archive-header">

  <h1><?php the_title(); ?></h1>

  <p class="archive-description">
Public admin tools for exploring site structure, taxonomy behavior, and a visual archive of published media attachments.
  </p>

  <nav class="admin-tools-nav"
       style="margin:1.5em 0 2em 0; display:flex; gap:16px; flex-wrap:wrap;">

    <!-- CHAPTERS BY SONG -->

    <a href="?tool=chapters-by-song"
       class="<?php echo $tool === 'chapters-by-song' ? 'active' : ''; ?>">
      Chapters by Song (Table)
    </a>

        <!-- FOOTNOTES -->

    <a href="?tool=footnotes"
       class="<?php echo $tool === 'footnotes' ? 'active' : ''; ?>">
      Footnotes Viewer
    </a>

    <!-- INDEX -->

    <a href="?tool=index"
       class="<?php echo $tool === 'index' ? 'active' : ''; ?>">
      Full Site Index
    </a>

    <!-- ACF INSPECTOR -->
    <a href="?tool=inspector"
       class="<?php echo $tool === 'inspector' ? 'active' : ''; ?>">
      Inspect Relationships
     </a>

    <!-- MEDIA LIBRARY -->

    <a href="?tool=media-library"
       class="<?php echo $tool === 'media-library' ? 'active' : ''; ?>">
      Media Library
    </a>

    <!-- LIVE FILTER -->

    <a href="?tool=live-filter"
       class="<?php echo $tool === 'live-filter' ? 'active' : ''; ?>">
      Live Content Filter
    </a>

    <!-- LIVE SEARCH -->

    <a href="?tool=live-search"
       class="<?php echo $tool === 'live-search' ? 'active' : ''; ?>">
      Live Content Search
    </a>

    <!-- NEWEST -->

    <a href="?tool=newest"
       class="<?php echo $tool === 'newest' ? 'active' : ''; ?>">
      Newest Content
    </a>

    <!-- ORPHANS -->

    <a href="?tool=orphans"
       class="<?php echo $tool === 'orphans' ? 'active' : ''; ?>">
      Orphaned CPTs
    </a>

    <!-- TAG AUDIT -->

    <a href="?tool=tag-audit"
       class="<?php echo $tool === 'tag-audit' ? 'active' : ''; ?>">
      Tag Audit
    </a>

    <!-- XML SITEMAP -->

    <a href="/sitemap_index.xml" target="_blank">
      XML Sitemap (Link)
    </a>

  </nav>

</header>

<?php

/*
|--------------------------------------------------------------------------
| Tool Loader
|--------------------------------------------------------------------------
*/

if ($tool === 'chapters-by-song') {

  get_template_part('template-parts/tools/tool', 'chapters-by-song');

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

elseif ($tool === 'tag-audit') {

  get_template_part('template-parts/tools/tool', 'tag-audit');

}

?>

</main>

<?php get_footer(); ?>