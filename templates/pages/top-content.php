<?php
/* Template Name: Top Content */

get_header();

/*
|--------------------------------------------------------------------------
| CURATED SECTIONS
|--------------------------------------------------------------------------
*/

$sections = [

    'threads' => [
        'title'       => 'Narrative Threads',
        'post_type'   => 'chapter',
        'theme_slug'  => 'top-threads',
        'layout'      => 'grid',
            'full_page'   => '/narrative-threads/',
        'description' => 'Curated narrative threads from across the archive.',
    ],

    'episodes' => [
        'title'       => 'Narrative Episodes',
        'post_type'   => 'fragment',
        'theme_slug'  => 'top-episodes',
        'layout'      => 'grid',
                'full_page'   => '/narrative-episodes/',
        'description' => 'Curated narrative episodes and fragments.',
    ],

    'lyrics' => [
        'title'       => 'Lyrics',
        'post_type'   => 'lyric',
        'theme_slug'  => 'top-lyrics',
        'layout'      => 'stream',
            'full_page' => '/song-excerpts/',
        'description' => 'Selected lyrical works from across the archive.',
    ],

    'excerpts' => [
        'title'       => 'Excerpts',
        'post_type'   => 'excerpt',
        'theme_slug'  => 'top-excerpts',
        'layout'      => 'stream',
            'full_page' => '/excerpt-library/',
        'description' => 'Selected excerpts and passages.',
    ],

    'images' => [
    'title'       => 'Images',
    'post_type'   => 'image',
    'theme_slug'  => 'top-images',
    'layout'      => 'image-grid',
        'full_page' => '/image-gallery/',
    'description' => 'Curated visual works from across the archive.',
],

    'portals' => [
    'title'       => 'Portals',
    'post_type'   => 'portal',
    'theme_slug'  => 'top-portals',
    'layout'      => 'grid',
        'full_page' => '/portals/',
    'description' => 'Curated works from across the archive.',
],

    'quotes' => [
        'title'       => 'Quotes',
        'post_type'   => 'quote',
        'theme_slug'  => 'top-quotes',
        'layout'      => 'stream',
            'full_page' => '/quote-library/',
        'description' => 'Selected quotes from the archive.',
    ],
];

/*
|--------------------------------------------------------------------------
| DEFAULT VIEW
|--------------------------------------------------------------------------
|
| Default to Narrative Threads
|
*/

$current = isset($_GET['view'])
    ? sanitize_key($_GET['view'])
    : 'threads';

if (!isset($sections[$current])) {
    $current = 'threads';
}

$config = $sections[$current];

/*
|--------------------------------------------------------------------------
| QUERY
|--------------------------------------------------------------------------
*/

$query = new WP_Query([

    'post_type'      => $config['post_type'],

    'posts_per_page' => 100,

    'post_status'    => 'publish',

    'orderby'        => 'title',

    'order'          => 'ASC',

    'tax_query' => [
        [
            'taxonomy' => 'theme',
            'field'    => 'slug',
            'terms'    => $config['theme_slug'],
        ]
    ]
]);

?>

<style>

/* ==========================================================================
   PAGE
   ========================================================================== */

.top-content-page {

    max-width: 1100px;

    margin: 0 auto;

    padding: 40px 20px 140px;
}

/* ==========================================================================
   HERO
   ========================================================================== */

.top-content-hero {

    margin-bottom: 26px;
}

.top-content-hero h1 {

    font-size: clamp(28px, 4vw, 52px);

    line-height: .92;

    margin: 0 0 20px;
}

.top-content-hero p {

    max-width: 760px;

    font-size: 15px;

    line-height: 1.7;

    opacity: .72;
}

/* ==========================================================================
   NAVIGATION
   ========================================================================== */

.top-content-nav {

    display: flex;

    flex-wrap: wrap;

    gap: 14px;

    margin-bottom: 10px;

    padding-bottom: 30px;

    border-bottom: 1px solid rgba(255,255,255,.08);
}

.top-content-nav a {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-height: 42px;

    padding: 10px 16px;

    border-radius: 999px;

    border: 1px solid rgba(255,255,255,.10);

    background: rgba(255,255,255,.03);

    text-decoration: none;

    font-size: 11px;

    font-weight: 700;

    letter-spacing: .14em;

    text-transform: uppercase;

    transition: .2s ease;
}

.top-content-nav a:hover {

    background: rgba(255,255,255,.07);

    border-color: rgba(255,255,255,.16);

    transform: translateY(-1px);
}

.top-content-nav a.active {

    background: #111;

    color: #fff;

    border-color: #111;

    box-shadow: 0 0 0 1px rgba(0,0,0,.15);

    opacity: 1;

}

/* ==========================================================================
   SECTION
   ========================================================================== */

.top-content-section {

    margin-bottom: 100px;
}

.top-content-section-header {

    margin-bottom: 22px;
}

.top-content-section-header h2 {

    font-size: clamp(24px, 3vw, 38px);

    line-height: 1;

    margin: 0 0 14px;
}

.top-content-section-header p {

    margin: 0;

    max-width: 720px;

    font-size: 14px;

    line-height: 1.8;

    opacity: .66;
}

/* ==========================================================================
   STREAM LAYOUT
   ========================================================================== */

.top-content-stream {

    display: flex;

    flex-direction: column;

    gap: 36px;
}

/* ==========================================================================
   STREAM ENTRY
   ========================================================================== */

.top-content-entry {

    display: flex;

    flex-direction: column;

    gap: 16px;
}

/* ==========================================================================
   ENTRY TITLE
   ========================================================================== */

.top-content-entry-title {

    margin: 0;
}

.top-content-entry-title a {

    text-decoration: none;

    font-size: 15px;

    letter-spacing: .08em;

    text-transform: uppercase;

    opacity: .78;

    transition: opacity .2s ease;
}

.top-content-entry-title a:hover {

    opacity: 1;
}

/* ==========================================================================
   ENTRY CONTENT
   ========================================================================== */

.top-content-entry-content {

    width: 100%;
}

.top-content-entry-content .wp-block-group:first-child,
.top-content-entry-content .wp-block-cover:first-child {

    margin-top: 0;
}

.top-content-entry-content .wp-block-group:last-child,
.top-content-entry-content .wp-block-cover:last-child {

    margin-bottom: 0;
}

/* ==========================================================================
   DIVIDER
   ========================================================================== */

.top-divider {

    margin-top: 4px;

    border-top: 1px solid rgba(255,255,255,.08);
}

/* ==========================================================================
   GRID LAYOUT
   ========================================================================== */

.top-content-grid {

    display: grid;

    grid-template-columns: repeat(2, minmax(0, 1fr));

    gap: 34px;
}

@media (max-width: 900px) {

    .top-content-grid {

        grid-template-columns: 1fr;
    }
}

/* ==========================================================================
   IMAGE GRID
   ========================================================================== */

.top-images-grid {

    display: grid;

    grid-template-columns: repeat(3, minmax(0, 1fr));

    gap: 30px;
}

@media (max-width: 900px) {

    .top-images-grid {

        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {

    .top-images-grid {

        grid-template-columns: 1fr;
    }
}

/* ==========================================================================
   IMAGE CARD
   ========================================================================== */

.top-image-card {

    display: flex;

    flex-direction: column;

    gap: 14px;
}

.top-image-card a {

    text-decoration: none;
}

.top-image-card img {

    width: 100%;

    aspect-ratio: 1 / 1;

    object-fit: cover;

    display: block;

    border-radius: 18px;

    background: rgba(255,255,255,.04);

    transition: transform .3s ease;
}

.top-image-card:hover img {

    transform: scale(1.02);
}

.top-image-card h3 {

    margin: 14px 0 0;

    font-size: 22px;

    line-height: 1.2;
}

.top-image-card p {

    margin: 0;

    opacity: .72;

    line-height: 1.6;

    font-size: 14px;
}

/* ==========================================================================
   GRID CARD
   ========================================================================== */

.top-grid-card {

    display: flex;

    flex-direction: column;

    gap: 18px;
}

.top-grid-card-image {

    position: relative;

    aspect-ratio: 16 / 10;

    overflow: hidden;

    border-radius: 18px;

    background: rgba(255,255,255,.04);
}

.top-grid-card-image img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    display: block;

    transition: transform .5s ease;
}

.top-grid-card:hover .top-grid-card-image img {

    transform: scale(1.03);
}

.top-grid-card-placeholder {

    width: 100%;

    height: 100%;

    background: rgba(255,255,255,.04);
}

.top-grid-card-title {

    margin: 0;
}

.top-grid-card-title a {

    text-decoration: none;

    font-size: clamp(26px, 4vw, 40px);

    line-height: 1.05;
}

/* ==========================================================================
   EMPTY
   ========================================================================== */

.top-content-empty {

    opacity: .65;

    font-size: 16px;
}

/* ==========================================================================
   FOOTER LINKS
   ========================================================================== */

.top-content-footer {

    padding-top: 1px;

    border-top: 1px solid rgba(255,255,255,.08);

    display: flex;

    flex-wrap: wrap;

    gap: 14px;
}

.top-content-footer a {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    padding: 14px 20px;

    border-radius: 999px;

    border: 1px solid rgba(255,255,255,.08);

    text-decoration: none;

    transition: .2s ease;
}

.top-content-footer a:hover {

    background: rgba(255,255,255,.05);
}

</style>

<main class="top-content-page">

    <header class="top-content-hero">

        <h1>Top Content</h1>

        <p>
            A curated selection of narrative threads, episodes,
            lyrics, excerpts, and quotes from across the archive.
        </p>

    </header>

    <nav class="top-content-nav">

        <?php foreach ($sections as $key => $section) : ?>

            <a
                href="?view=<?php echo esc_attr($key); ?>"
                class="<?php echo $current === $key ? 'active' : ''; ?>"
            >
                <?php echo esc_html($section['title']); ?>
            </a>

        <?php endforeach; ?>

    </nav>

    <section class="top-content-section">

        <header class="top-content-section-header">

            <h2>
                <?php echo esc_html($config['title']); ?>
            </h2>

            <p>
                <?php echo esc_html($config['description']); ?>
            </p>

        </header>

        <?php if ($query->have_posts()) : ?>

<?php if ($config['layout'] === 'grid') : ?>

    <div class="top-content-grid">

        <?php while ($query->have_posts()) : $query->the_post(); ?>

            <article class="top-grid-card">

                <a
                    class="top-grid-card-image"
                    href="<?php the_permalink(); ?>"
                >

                    <?php if (has_post_thumbnail()) : ?>

                        <?php the_post_thumbnail('large'); ?>

                    <?php else : ?>

                        <div class="top-grid-card-placeholder"></div>

                    <?php endif; ?>

                </a>

                <h3 class="top-grid-card-title">

                    <a href="<?php the_permalink(); ?>">

                        <?php the_title(); ?>

                    </a>

                </h3>

            </article>

        <?php endwhile; ?>

    </div>

<?php elseif ($config['layout'] === 'image-grid') : ?>

    <div class="top-images-grid">

        <?php while ($query->have_posts()) : $query->the_post(); ?>

            <?php

            $caption = get_field('image_caption');

            $image = get_field('image_file');

            $img_url = $image
                ? $image['sizes']['large']
                : get_the_post_thumbnail_url(get_the_ID(), 'large');

            ?>

            <article class="top-image-card">

                <a href="<?php the_permalink(); ?>">

                    <?php if ($img_url) : ?>

                        <img
                            src="<?php echo esc_url($img_url); ?>"
                            alt="<?php the_title(); ?>"
                        >

                    <?php endif; ?>

                    <h3>

                        <?php the_title(); ?>

                    </h3>

                </a>

                <?php if ($caption) : ?>

                    <p>

                        <?php echo esc_html(wp_trim_words($caption, 20)); ?>

                    </p>

                <?php endif; ?>

            </article>

        <?php endwhile; ?>

    </div>

<?php else : ?>

                <div class="top-content-stream">

                    <?php while ($query->have_posts()) : $query->the_post(); ?>

                        <article class="top-content-entry">

                            <h3 class="top-content-entry-title">

                                <a href="<?php the_permalink(); ?>">

                                    <?php the_title(); ?>

                                </a>

                            </h3>

                            <div class="top-content-entry-content">

                                <?php the_content(); ?>

                            </div>

                            <div class="top-divider"></div>

                        </article>

                    <?php endwhile; ?>

                </div>

            <?php endif; ?>

            <?php wp_reset_postdata(); ?>

        <?php else : ?>

            <p class="top-content-empty">
                No curated entries found.
            </p>

        <?php endif; ?>

    </section>

<footer class="top-content-footer">

    <a href="<?php echo esc_url($config['full_page']); ?>">

        Explore Full <?php echo esc_html($config['title']); ?>

    </a>

</footer>

</main>

<?php get_footer(); ?>