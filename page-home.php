<?php
/* Template Name: Custom Home */

get_header();

$homepage_sections = site_get_navigation_sections();
?>

<main class="homepage-posts homepage-sectioned">

<?php foreach ($homepage_sections as $section_title => $pages) : ?>

    <?php
    get_template_part('template-parts/page', 'grid', [
        'title' => $section_title,
        'pages' => $pages,
    ]);
    ?>

<?php endforeach; ?>

</main>

<?php get_footer(); ?>