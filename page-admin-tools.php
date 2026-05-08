<?php
/* Template Name: Admin Tools */
get_header();

$tool = $_GET['tool'] ?? 'orphans';

$allowed_tools = [
  'orphans',
  'tag-audit',
  'media-library'
];

if (!in_array($tool, $allowed_tools)) {
  $tool = 'orphans';
}
?>

<main class="cpt-index-clean">

<header class="archive-header">
  <h1><?php the_title(); ?></h1>

  <nav class="admin-tools-nav" style="margin:1.5em 0 2em 0; display:flex; gap:16px; flex-wrap:wrap;">

    <a href="?tool=orphans"
       class="<?php echo $tool === 'orphans' ? 'active' : ''; ?>">
      Orphans
    </a>

    <a href="?tool=media-library"
       class="<?php echo $tool === 'media-library' ? 'active' : ''; ?>">
      Media Library
    </a>

    <a href="?tool=tag-audit"
       class="<?php echo $tool === 'tag-audit' ? 'active' : ''; ?>">
      Tag Audit
    </a>

  </nav>
</header>

<?php

if ($tool === 'orphans') {
  get_template_part('template-parts/tools/tool', 'orphans');
}

elseif ($tool === 'media-library') {
  get_template_part('template-parts/tools/tool', 'media-library');
}

elseif ($tool === 'tag-audit') {
  get_template_part('template-parts/tools/tool', 'tag-audit');
}

?>

</main>

<?php get_footer(); ?>