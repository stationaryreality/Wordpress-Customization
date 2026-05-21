<?php
/* Template Name: Portal Knowledge Atlas */

get_header();
the_post();

/*
|--------------------------------------------------------------------------
| PORTAL TERMS
|--------------------------------------------------------------------------
*/

$portal_terms = [];
$taxonomies   = get_object_taxonomies(get_post_type());

foreach ($taxonomies as $taxonomy) {

    $terms = wp_get_post_terms(
        get_the_ID(),
        $taxonomy,
        ['fields' => 'slugs']
    );

    if (!empty($terms) && !is_wp_error($terms)) {
        $portal_terms[$taxonomy] = $terms;
    }
}

if (empty($portal_terms)) : ?>

    <main class="portal-atlas">
        <div class="portal-shell">

            <header class="portal-hero">
                <h1><?php the_title(); ?></h1>
            </header>

            <p>No taxonomy relationships found.</p>

        </div>
    </main>

<?php
    get_footer();
    exit;
endif;

/*
|--------------------------------------------------------------------------
| CPT METADATA
|--------------------------------------------------------------------------
*/

$map = get_cpt_metadata();

/*
|--------------------------------------------------------------------------
| INCLUDED CPTS
|--------------------------------------------------------------------------
*/

$post_types = [
    'concept',
    'quote',
    'song',
    'book',
    'movie',
    'excerpt',
    'lyric',
    'image',
    'element',
];

/*
|--------------------------------------------------------------------------
| SECTION LABELS
|--------------------------------------------------------------------------
*/

$section_labels = [

    'concept' => 'Concepts',
    'quote'   => 'Quotes',
    'song'    => 'Songs',
    'book'    => 'Books',
    'movie'   => 'Movies',
    'excerpt' => 'Excerpts',
    'lyric'   => 'Lyrics',
    'image'   => 'Images',
    'element' => 'Elements',
];

/*
|--------------------------------------------------------------------------
| SECTION ORDER
|--------------------------------------------------------------------------
*/

$section_order = [
    'concept',
    'quote',
    'song',
    'book',
    'movie',
    'excerpt',
    'lyric',
    'image',
    'element',
];

/*
|--------------------------------------------------------------------------
| TAX QUERY
|--------------------------------------------------------------------------
*/

$tax_query = ['relation' => 'OR'];

foreach ($portal_terms as $taxonomy => $slugs) {

    $tax_query[] = [
        'taxonomy' => $taxonomy,
        'field'    => 'slug',
        'terms'    => $slugs,
    ];
}

/*
|--------------------------------------------------------------------------
| QUERY
|--------------------------------------------------------------------------
*/

$args = [
    'post_type'      => $post_types,
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'title',
    'order'          => 'ASC',
    'tax_query'      => $tax_query,
];

$query = new WP_Query($args);

/*
|--------------------------------------------------------------------------
| SECTION STORAGE
|--------------------------------------------------------------------------
*/

$sections = [];
$total_entries = 0;

foreach ($section_order as $type) {
    $sections[$type] = [];
}

/*
|--------------------------------------------------------------------------
| BUILD DATA
|--------------------------------------------------------------------------
*/

if ($query->have_posts()) :

    while ($query->have_posts()) :

        $query->the_post();

        $post_id = get_the_ID();
        $type    = get_post_type();

        if ($type === 'portal') {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | DEFAULTS
        |--------------------------------------------------------------------------
        */

        $title    = get_the_title();
        $url      = get_permalink();
        $icon     = $map[$type]['emoji'] ?? '✦';
        $excerpt  = '';
        $image    = '';
        $meta     = '';

        /*
        |--------------------------------------------------------------------------
        | CONCEPTS
        |--------------------------------------------------------------------------
        */

        if ($type === 'concept') {

            $excerpt = get_field('definition');

            $image = has_post_thumbnail()
                ? get_the_post_thumbnail_url($post_id, 'medium')
                : '';
        }

        /*
        |--------------------------------------------------------------------------
        | QUOTES
        |--------------------------------------------------------------------------
        */

        elseif ($type === 'quote') {

            $excerpt = get_field('quote_plain_text');

            $source = get_field('source');

            if ($source) {

                if (is_array($source)) {
                    $source = reset($source);
                }

                $cover = get_field('cover_image', $source->ID);

                if ($cover && is_array($cover)) {

                    $image =
                        $cover['sizes']['medium']
                        ?? $cover['sizes']['thumbnail']
                        ?? $cover['url'];

                } elseif (has_post_thumbnail($source->ID)) {

                    $image = get_the_post_thumbnail_url($source->ID, 'medium');
                }
            }

            if (!$image && has_post_thumbnail($post_id)) {

                $image = get_the_post_thumbnail_url($post_id, 'medium');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SONGS
        |--------------------------------------------------------------------------
        */

        elseif ($type === 'song') {

            $artist = get_field('song_artist');

            if ($artist) {

                if (is_array($artist)) {
                    $artist = reset($artist);
                }

                $meta = get_the_title($artist->ID);
            }

            $cover = get_field('cover_image');

            $image = $cover
                ? $cover['sizes']['medium']
                : get_the_post_thumbnail_url($post_id, 'medium');
        }

        /*
        |--------------------------------------------------------------------------
        | BOOKS
        |--------------------------------------------------------------------------
        */

        elseif ($type === 'book') {

            $meta = get_field('author');

            $cover = get_field('cover_image');

            $image = $cover
                ? $cover['sizes']['medium']
                : '';
        }

        /*
        |--------------------------------------------------------------------------
        | MOVIES
        |--------------------------------------------------------------------------
        */

        elseif ($type === 'movie') {

            $cover = get_field('cover_image');

            $image = $cover
                ? $cover['sizes']['medium']
                : get_the_post_thumbnail_url($post_id, 'medium');
        }

        /*
        |--------------------------------------------------------------------------
        | EXCERPTS
        |--------------------------------------------------------------------------
        */

elseif ($type === 'excerpt') {

    $excerpt = get_field('excerpt_plain_text');

    $source = get_field('excerpt_source');

    $author_name = '';

    if ($source && get_post_type($source->ID) === 'book') {

        $author = get_field('author_profile', $source->ID);

        if ($author) {

            if (is_array($author)) {
                $author = reset($author);
            }

            $author_name = get_the_title($author->ID);
        }
    }

    $meta = $author_name;

    /*
    |--------------------------------------------------------------------------
    | IMAGE LOGIC
    |--------------------------------------------------------------------------
    |
    | Prefer:
    | 1. Source cover_image
    | 2. Source featured image
    | 3. Excerpt featured image
    |
    */

    if ($source) {

        $cover = get_field('cover_image', $source->ID);

        if ($cover && is_array($cover)) {

            $image =
                $cover['sizes']['medium']
                ?? $cover['sizes']['thumbnail']
                ?? $cover['url'];

        } elseif (has_post_thumbnail($source->ID)) {

            $image = get_the_post_thumbnail_url($source->ID, 'medium');
        }
    }

    if (!$image && has_post_thumbnail($post_id)) {

        $image = get_the_post_thumbnail_url($post_id, 'medium');
    }
}

        /*
        |--------------------------------------------------------------------------
        | LYRICS
        |--------------------------------------------------------------------------
        */

        elseif ($type === 'lyric') {

            $excerpt = get_field('lyric_plain_text');

            $song = get_field('song');

            if ($song) {

                $song_title = get_the_title($song->ID);

                $artist = get_field('song_artist', $song->ID);

                if ($artist) {

                    if (is_array($artist)) {
                        $artist = reset($artist);
                    }

                    $meta = get_the_title($artist->ID);
                }

                $cover = get_field('cover_image', $song->ID);

                if ($cover && is_array($cover)) {

                    $image =
                        $cover['sizes']['medium']
                        ?? $cover['sizes']['thumbnail']
                        ?? $cover['url'];

                } elseif (has_post_thumbnail($song->ID)) {

                    $image = get_the_post_thumbnail_url($song->ID, 'medium');
                }
            }

            if (!$image && has_post_thumbnail($post_id)) {

                $image = get_the_post_thumbnail_url($post_id, 'medium');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | IMAGES
        |--------------------------------------------------------------------------
        */

        elseif ($type === 'image') {

            $excerpt = get_field('image_caption');

            $image_field = get_field('image_file');

            $image = $image_field
                ? $image_field['sizes']['medium']
                : get_the_post_thumbnail_url($post_id, 'medium');
        }

        /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        elseif ($type === 'element') {

            $image_field = get_field('image_file') ?: get_post_thumbnail_id();

            if (is_array($image_field)) {

                $image =
                    $image_field['sizes']['medium']
                    ?? $image_field['url'];

            } elseif ($image_field) {

                $image = wp_get_attachment_image_url($image_field, 'medium');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | STORE
        |--------------------------------------------------------------------------
        */

        $sections[$type][] = [

            'title'   => $title,
            'url'     => $url,
            'icon'    => $icon,
            'excerpt' => $excerpt,
            'image'   => $image,
            'meta'    => $meta,
            'type'    => $type,
        ];

        $total_entries++;

    endwhile;

    wp_reset_postdata();

endif;

/*
|--------------------------------------------------------------------------
| ACTIVE SECTIONS
|--------------------------------------------------------------------------
*/

$active_sections = [];

foreach ($sections as $type => $entries) {

    if (!empty($entries)) {
        $active_sections[$type] = count($entries);
    }
}

?>

<main class="portal-atlas">

    <div class="portal-shell">

        <!-- HERO -->

        <header class="portal-hero">

            <div class="portal-hero-inner">

                <p class="portal-kicker">
                    Knowledge Atlas
                </p>

                <h1 class="portal-title">
                    <?php the_title(); ?>
                </h1>

                <?php if (has_excerpt()) : ?>

                    <div class="portal-description">
                        <?php the_excerpt(); ?>
                    </div>

                <?php endif; ?>

                <div class="portal-meta-grid">

                    <div class="portal-meta-card">

                        <span class="portal-meta-number">
                            <?php echo esc_html($total_entries); ?>
                        </span>

                        <span class="portal-meta-label">
                            Related Entries
                        </span>

                    </div>

                    <div class="portal-meta-card">

                        <span class="portal-meta-number">
                            <?php echo esc_html(count($active_sections)); ?>
                        </span>

                        <span class="portal-meta-label">
                            Active Sections
                        </span>

                    </div>

                </div>

            </div>

        </header>

        <!-- NAV -->

        <?php if (!empty($active_sections)) : ?>

            <nav class="portal-section-nav">

                <?php foreach ($active_sections as $type => $count) : ?>

                    <a
                        href="#section-<?php echo esc_attr($type); ?>"
                        class="portal-nav-pill"
                    >

                        <span>
                            <?php
                            echo esc_html(
                                $section_labels[$type]
                                ?? ucfirst($type)
                            );
                            ?>
                        </span>

                        <strong>
                            <?php echo esc_html($count); ?>
                        </strong>

                    </a>

                <?php endforeach; ?>

            </nav>

        <?php endif; ?>

        <!-- SECTIONS -->

        <div class="portal-sections">

            <?php foreach ($section_order as $type) :

                $entries = $sections[$type];

                if (empty($entries)) {
                    continue;
                }

                $label = $section_labels[$type] ?? ucfirst($type);

            ?>

                <section
                    class="portal-section"
                    id="section-<?php echo esc_attr($type); ?>"
                >

                    <header class="portal-section-header">

                        <h2><?php echo esc_html($label); ?></h2>

                        <span class="portal-section-count">
                            <?php echo count($entries); ?>
                        </span>

                    </header>

                    <div class="portal-card-grid">

                        <?php foreach ($entries as $entry) : ?>

                            <article class="portal-card">

                                <a
                                    href="<?php echo esc_url($entry['url']); ?>"
                                    class="portal-card-inner"
                                >

                                    <?php if (!empty($entry['image'])) : ?>

                                        <div class="portal-card-image">

                                            <img
                                                src="<?php echo esc_url($entry['image']); ?>"
                                                alt="<?php echo esc_attr($entry['title']); ?>"
                                            >

                                        </div>

                                    <?php endif; ?>

                                    <div class="portal-card-content">

                                        <div class="portal-card-top">

                                            <span class="portal-card-icon">
                                                <?php echo esc_html($entry['icon']); ?>
                                            </span>

                                            <span class="portal-card-type">

                                                <?php
                                                echo esc_html(
                                                    $map[$entry['type']]['title']
                                                    ?? ucfirst($entry['type'])
                                                );
                                                ?>

                                            </span>

                                        </div>

                                        <h3 class="portal-card-title">
                                            <?php echo esc_html($entry['title']); ?>
                                        </h3>

                                        <?php if (!empty($entry['meta'])) : ?>

                                            <div class="portal-card-meta">
                                                <?php echo esc_html($entry['meta']); ?>
                                            </div>

                                        <?php endif; ?>

                                        <?php if (!empty($entry['excerpt'])) : ?>

                                            <div class="portal-card-excerpt">

                                                <?php
                                                echo wp_trim_words(
                                                    wp_strip_all_tags($entry['excerpt']),
                                                    40
                                                );
                                                ?>

                                            </div>

                                        <?php endif; ?>

                                    </div>

                                </a>

                            </article>

                        <?php endforeach; ?>

                    </div>

                </section>

            <?php endforeach; ?>

        </div>

    </div>

</main>

<style>

/* =========================================================
   BASE
========================================================= */

.portal-atlas {
    padding: 50px 24px 120px;
}

.portal-shell {
    width: min(1600px, 100%);
    margin: 0 auto;
}

/* =========================================================
   HERO
========================================================= */

.portal-hero {
    margin-bottom: 50px;
}

.portal-hero-inner {

    padding: 48px;

    border-radius: 32px;

    border: 1px solid rgba(255,255,255,.08);

    background:
        linear-gradient(
            180deg,
            rgba(255,255,255,.04),
            rgba(255,255,255,.015)
        );
}

.portal-kicker {

    margin: 0 0 12px;

    text-transform: uppercase;

    letter-spacing: .16em;

    font-size: 11px;

    opacity: .55;
}

.portal-title {

    margin: 0;

    font-size: clamp(48px, 10vw, 100px);

    line-height: .95;
}

.portal-description {

    margin-top: 22px;

    max-width: 850px;

    font-size: 18px;

    line-height: 1.8;

    opacity: .82;
}

.portal-meta-grid {

    display: flex;

    gap: 18px;

    flex-wrap: wrap;

    margin-top: 40px;
}

.portal-meta-card {

    min-width: 180px;

    padding: 20px;

    border-radius: 18px;

    background: rgba(255,255,255,.03);

    border: 1px solid rgba(255,255,255,.06);
}

.portal-meta-number {

    display: block;

    font-size: 34px;

    font-weight: 700;
}

.portal-meta-label {

    display: block;

    margin-top: 6px;

    opacity: .65;
}

/* =========================================================
   NAV
========================================================= */

.portal-section-nav {

    display: flex;

    gap: 12px;

    flex-wrap: wrap;

    margin-bottom: 70px;
}

.portal-nav-pill {

    display: inline-flex;

    align-items: center;

    gap: 10px;

    padding: 12px 18px;

    border-radius: 999px;

    text-decoration: none;

    background: rgba(255,255,255,.03);

    border: 1px solid rgba(255,255,255,.08);

    transition:
        transform .15s ease,
        background .15s ease;
}

.portal-nav-pill:hover {

    transform: translateY(-2px);

    background: rgba(255,255,255,.06);
}

/* =========================================================
   SECTION
========================================================= */

.portal-section {
    margin-bottom: 48px;
}

.portal-section-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-bottom: 28px;
}

.portal-section-header h2 {

    margin: 0;

    font-size: 38px;
}

.portal-section-count {
    opacity: .5;
}

/* =========================================================
   GRID
========================================================= */

.portal-card-grid {

    display: grid;

    grid-template-columns:
        repeat(auto-fill, minmax(200px, 1fr));

    gap: 20px;
}

/* =========================================================
   CARD
========================================================= */

.portal-card {
    height: 100%;
}

.portal-card-inner {

    display: flex;

    flex-direction: column;

    overflow: hidden;

    border-radius: 26px;

    text-decoration: none;

    border: 1px solid rgba(255,255,255,.08);

    background:
        linear-gradient(
            180deg,
            rgba(255,255,255,.04),
            rgba(255,255,255,.015)
        );

    transition:
        transform .18s ease,
        border-color .18s ease,
        background .18s ease;
}

.portal-card-inner:hover {

    transform: translateY(-4px);

    border-color: rgba(255,255,255,.16);

    background:
        linear-gradient(
            180deg,
            rgba(255,255,255,.06),
            rgba(255,255,255,.025)
        );
}

/* =========================================================
   IMAGE
========================================================= */

.portal-card-image {

    width: 100%;

    max-height: 140px;

    overflow: hidden;

    background: rgba(255,255,255,.03);

    border-bottom: 1px solid rgba(255,255,255,.06);
}

.portal-card-image img {

    width: 100%;
    height: 140px;

    object-fit: cover;

    display: block;
}

/* =========================================================
   CONTENT
========================================================= */

.portal-card-content {

    display: flex;

    flex-direction: column;

    gap: 12px;

    padding: 18px;

    height: 100%;
}

.portal-card-top {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 10px;
}

.portal-card-icon {
    font-size: 24px;
}

.portal-card-type {

    font-size: 11px;

    letter-spacing: .16em;

    text-transform: uppercase;

    opacity: .5;
}

.portal-card-title {

    margin: 0;

    font-size: 20px;

    line-height: 1.25;
}

.portal-card-meta {

    font-size: 14px;

    font-weight: 600;

    opacity: .72;
}

.portal-card-excerpt {

    font-size: 14px;

    line-height: 1.6;

    opacity: .78;

    display: -webkit-box;

    -webkit-line-clamp: 4;

    -webkit-box-orient: vertical;

    overflow: hidden;
}

/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 800px) {

    .portal-atlas {
        padding: 30px 18px 90px;
    }

    .portal-hero-inner {
        padding: 28px;
    }

    .portal-section-header h2 {
        font-size: 28px;
    }

    .portal-card-grid {

        grid-template-columns: 1fr;
    }

}

</style>

<?php get_footer(); ?>