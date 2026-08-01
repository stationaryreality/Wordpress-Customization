<?php
$profile_id = get_queried_object_id();
$bio        = get_field('bio', $profile_id);
$portrait   = get_field('portrait_image', $profile_id);
$img_url    = $portrait ? $portrait['sizes']['thumbnail'] : '';
$wiki_slug  = get_field('wikipedia_slug', $profile_id);

// === Gather Authored Content ===
$books = get_posts([
    'post_type'      => 'book',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'meta_query'     => [['key' => 'author_profile', 'value' => $profile_id, 'compare' => '=']]
]);

$reference_ids = get_posts([
    'post_type'      => 'reference',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'meta_query'     => [['key' => 'author_profile', 'value' => $profile_id, 'compare' => '=']]
]);

$sources = array_unique(array_merge($books ?: [], $reference_ids ?: []));

// === Fetch Quotes ===
$quote_meta = ['relation' => 'OR'];
if (!empty($sources)) {
    $quote_meta[] = ['key' => 'quote_source', 'value' => $sources, 'compare' => 'IN'];
}
$quote_meta[] = ['key' => 'author_profile', 'value' => $profile_id, 'compare' => '='];
$quote_meta[] = ['key' => 'related_profiles', 'value' => '"' . $profile_id . '"', 'compare' => 'LIKE'];

$quotes = get_posts(['post_type' => 'quote', 'posts_per_page' => -1, 'meta_query' => $quote_meta, 'orderby' => 'title', 'order' => 'ASC']);

// === Fetch Excerpts ===
$excerpt_meta = ['relation' => 'OR'];
if (!empty($sources)) {
    $excerpt_meta[] = ['key' => 'excerpt_source', 'value' => $sources, 'compare' => 'IN'];
}
$excerpt_meta[] = ['key' => 'author_profile', 'value' => $profile_id, 'compare' => '='];
$excerpt_meta[] = ['key' => 'related_profiles', 'value' => '"' . $profile_id . '"', 'compare' => 'LIKE'];

$excerpts = get_posts(['post_type' => 'excerpt', 'posts_per_page' => -1, 'meta_query' => $excerpt_meta, 'orderby' => 'title', 'order' => 'ASC']);

// === Fetch References ===
$references = get_posts([
    'post_type'      => 'reference',
    'posts_per_page' => -1,
    'meta_query'     => [['key' => 'author_profile', 'value' => $profile_id, 'compare' => '=']],
    'orderby'        => 'date',
    'order'          => 'DESC'
]);

// Unique filters
$seen_quotes = [];
$unique_quotes = array_filter($quotes, function($q) use (&$seen_quotes) {
    if (in_array($q->ID, $seen_quotes)) return false;
    $seen_quotes[] = $q->ID; return true;
});

$seen_excerpts = [];
$unique_excerpts = array_filter($excerpts, function($e) use (&$seen_excerpts) {
    if (in_array($e->ID, $seen_excerpts)) return false;
    $seen_excerpts[] = $e->ID; return true;
});
?>

<div class="person-content cpt-profile-content">

    <?php get_template_part('template-parts/navigation/profile'); ?>

    <div class="cpt-profile-header">
        <?php if ($img_url): ?>
            <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="cpt-profile-portrait">
        <?php endif; ?>
        <h1 class="cpt-profile-title"><?php the_title(); ?></h1>
    </div>

    <div class="cpt-profile-bio">
        <?php
        if ($bio) {
            echo wp_kses_post($bio);
        } elseif ($wiki_slug) {
            $wiki_intro = kp_get_wikipedia_intro($wiki_slug);
            if ($wiki_intro) echo '<p>' . esc_html($wiki_intro) . '</p>';
        }
        ?>
    </div>

    <?php if (!empty($unique_quotes)): ?>
        <?php get_template_part('template-parts/render/content-objects', null, ['posts' => $unique_quotes, 'title' => 'Quotes']); ?>
    <?php endif; ?>

    <?php if (!empty($unique_excerpts)): ?>
        <?php get_template_part('template-parts/render/content-objects', null, ['posts' => $unique_excerpts, 'title' => 'Excerpts']); ?>
    <?php endif; ?>

    <?php if (!empty($references)): ?>
        <div class="cpt-profile-related-section">
            <h2 class="cpt-profile-related-title">Content by <?php the_title(); ?></h2>
            <div class="cpt-profile-related-grid">
                <?php foreach ($references as $ref): 
                    $thumb = get_the_post_thumbnail_url($ref->ID, 'medium');
                ?>
                    <a href="<?php echo esc_url(get_permalink($ref->ID)); ?>" class="cpt-profile-related-item">
                        <?php if ($thumb): ?>
                            <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($ref->ID)); ?>" class="cpt-profile-related-image">
                        <?php endif; ?>
                        <span class="cpt-profile-related-name"><?php echo esc_html(get_the_title($ref->ID)); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($books)): 
        $book_query = new WP_Query(['post_type' => 'book', 'post__in' => $books, 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);
        if ($book_query->have_posts()): ?>
            <div class="cpt-profile-related-section">
                <h2 class="cpt-profile-related-title">Books by <?php the_title(); ?></h2>
                <div class="cpt-profile-related-grid">
                    <?php while ($book_query->have_posts()): $book_query->the_post(); 
                        $cover = get_field('cover_image');
                        $img = $cover ? $cover['sizes']['medium'] : '';
                    ?>
                        <a href="<?php the_permalink(); ?>" class="cpt-profile-related-item">
                            <?php if ($img): ?>
                                <img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>" class="cpt-profile-related-image">
                            <?php endif; ?>
                            <span class="cpt-profile-related-name"><?php the_title(); ?></span>
                        </a>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        <?php endif;
    endif; ?>

    <?php show_featured_in_threads('people_referenced'); ?>

</div>