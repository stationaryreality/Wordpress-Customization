<?php
/**
 * Standalone Portal Template
 * ------------------------------------------------------------
 * Does not rely on single-portal.php, metadata files, or shared
 * template logic. All logic is contained inside this file.
 */

get_header();

// Ensure we have the portal post
global $post;
if (! $post) {
    $post = get_queried_object();
}
setup_postdata($post);

// ============================================================
// 1. GET TERMS FOR THIS PORTAL
// ============================================================
$portal_id = $post->ID;
$portal_title = get_the_title($portal_id);

$taxonomies = get_object_taxonomies('portal');
$portal_terms = [];

foreach ($taxonomies as $taxonomy) {
    $slugs = wp_get_post_terms($portal_id, $taxonomy, ['fields' => 'slugs']);
    if (!empty($slugs) && !is_wp_error($slugs)) {
        $portal_terms = array_merge($portal_terms, $slugs);
    }
}

if (empty($portal_terms)) {
    echo "<main class='portal-index'><h1 class='center'>{$portal_title} Portal</h1><p>No terms found.</p></main>";
    get_footer();
    return;
}

// ============================================================
// Helper — build a tax_query for all non-portal CPT queries
// ============================================================
function portal_tax_query($terms) {
    return [
        'relation' => 'OR',
        [
            'taxonomy' => 'topic',
            'field'    => 'slug',
            'terms'    => $terms,
        ],
        [
            'taxonomy' => 'theme',
            'field'    => 'slug',
            'terms'    => $terms,
        ],
    ];
}

// ============================================================
// 2. FIND THE EXACT-MATCH CONCEPT CPT
// ============================================================
$concept_query = new WP_Query([
    'post_type'      => 'concept',
    'posts_per_page' => 1,
    'post_status'    => 'publish',
    'tax_query'      => portal_tax_query($portal_terms),
]);

?>
<main class="portal-index">

    <!-- =============================== -->
    <!-- PORTAL TITLE CENTERED -->
    <!-- =============================== -->
    <header class="portal-header" style="text-align:center; margin-bottom:3rem;">
        <h1><?php echo esc_html($portal_title); ?> Portal</h1>
    </header>

    <!-- =============================== -->
    <!-- LEXICON / CONCEPT SECTION (NO TITLE) -->
    <!-- =============================== -->
    <?php if ($concept_query->have_posts()) : ?>
        <?php
        $concept_query->the_post();
        echo "<div class='portal-concept-section'>";
        the_content();   // render the cover block exactly as stored
        echo "</div>";
        wp_reset_postdata();
        ?>
        <hr class="portal-divider">
    <?php endif; ?>


    <!-- ===================================================== -->
    <!-- SECTION RENDERER FOR FULL-CONTENT CPT SECTIONS -->
    <!-- ===================================================== -->
    <?php
    function portal_render_full_section($emoji, $label, $cpt, $terms, $portal_id) {

        $q = new WP_Query([
            'post_type'      => $cpt,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
            'tax_query'      => portal_tax_query($terms),
        ]);

        if (! $q->have_posts()) return;

        echo "<section class='portal-section' style='margin:3rem 0; text-align:center;'>";
        echo "<h2>{$emoji} {$label}</h2>";

        while ($q->have_posts()) {
            $q->the_post();

            // skip portal recursion
            if (get_post_type() === 'portal') continue;
            if (get_the_ID() === $portal_id) continue;

            echo "<div class='portal-entry-content' style='margin:2rem 0;'>";
            the_content();
            echo "</div>";

            echo "<hr class='portal-divider'>";
        }

        wp_reset_postdata();
        echo "</section>";
    }
    ?>


    <!-- =============================== -->
    <!-- QUOTES -->
    <!-- =============================== -->
    <?php portal_render_full_section("💬", "Quotes", "quote", $portal_terms, $portal_id); ?>

    <!-- =============================== -->
    <!-- EXCERPTS -->
    <!-- =============================== -->
    <?php portal_render_full_section("📖", "Excerpts", "excerpt", $portal_terms, $portal_id); ?>

    <!-- =============================== -->
    <!-- SONG EXCERPTS -->
    <!-- =============================== -->
    <?php portal_render_full_section("🎼", "Song Excerpts", "lyric", $portal_terms, $portal_id); ?>


    <!-- ===================================================== -->
    <!-- GRID RENDERER (IMAGES & BOOKS) -->
    <!-- ===================================================== -->
    <?php
    function portal_render_grid($emoji, $label, $cpt, $terms, $portal_id) {

        $q = new WP_Query([
            'post_type'      => $cpt,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
            'tax_query'      => portal_tax_query($terms),
        ]);

        if (! $q->have_posts()) return;

        echo "<section class='portal-grid-section' style='margin:3rem 0;'>";
        echo "<h2 style='text-align:center;'>{$emoji} {$label}</h2>";

        echo "<div class='portal-grid' style='display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:1.5rem; margin-top:2rem;'>";

        while ($q->have_posts()) {
            $q->the_post();

            if (get_post_type() === 'portal') continue;
            if (get_the_ID() === $portal_id) continue;

            echo "<div class='portal-grid-item'>";
            if (has_post_thumbnail()) {
                echo '<a href="' . get_permalink() . '">';
                the_post_thumbnail('medium');
                echo '</a>';
            }
            echo '<h3 style="text-align:center; margin-top:0.5rem;">';
            echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
            echo '</h3>';
            echo "</div>";
        }

        echo "</div>";

        wp_reset_postdata();
        echo "</section>";
    }
    ?>

    <!-- =============================== -->
    <!-- IMAGES GRID -->
    <!-- =============================== -->
    <?php portal_render_grid("🖼", "Images", "image", $portal_terms, $portal_id); ?>

    <!-- =============================== -->
    <!-- BOOKS GRID -->
    <!-- =============================== -->
    <?php portal_render_grid("📚", "Books", "book", $portal_terms, $portal_id); ?>

</main>

<?php get_footer(); ?>
