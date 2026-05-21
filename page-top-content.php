<?php
/* Template Name: Top Content */

get_header();

/*
|--------------------------------------------------------------------------
| CONFIG
|--------------------------------------------------------------------------
|
| Uses manually assigned THEME terms:
|
| - top-quotes
| - top-excerpts
| - top-lyrics
|
| Assign those theme terms manually to curate content.
|
*/

$sections = [

    'quotes' => [
        'label'      => 'Quotes',
        'post_type'  => 'quote',
        'theme_slug' => 'top-quotes',
    ],

    'excerpts' => [
        'label'      => 'Excerpts',
        'post_type'  => 'excerpt',
        'theme_slug' => 'top-excerpts',
    ],

    'lyrics' => [
        'label'      => 'Lyrics',
        'post_type'  => 'lyric',
        'theme_slug' => 'top-lyrics',
    ],
];

$current = isset($_GET['view'])
    ? sanitize_key($_GET['view'])
    : 'quotes';

if (!isset($sections[$current])) {
    $current = 'quotes';
}

$config = $sections[$current];

/*
|--------------------------------------------------------------------------
| QUERY
|--------------------------------------------------------------------------
*/

$query = new WP_Query([
    'post_type'      => $config['post_type'],
    'posts_per_page' => 25,
    'post_status'    => 'publish',

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

.top-content-page {

    max-width: 900px;

    margin: 0 auto;

    padding: 50px 24px 100px;
}

/* ==========================================================================
   HERO
   ========================================================================== */

.top-content-hero {

    margin-bottom: 60px;
}

.top-content-hero h1 {

    font-size: clamp(40px, 5vw, 72px);

    line-height: 1;

    margin: 0 0 18px;
}

.top-content-hero p {

    font-size: 18px;

    line-height: 1.7;

    opacity: .7;

    max-width: 720px;
}

/* ==========================================================================
   TOP NAV
   ========================================================================== */

.top-content-nav {

    display: flex;

    gap: 14px;

    flex-wrap: wrap;

    margin-bottom: 60px;

    padding-bottom: 24px;

    border-bottom: 1px solid rgba(255,255,255,.08);
}

.top-content-nav a {

    text-decoration: none;

    padding: 12px 18px;

    border-radius: 999px;

    border: 1px solid rgba(255,255,255,.08);

    transition: .2s ease;

    font-size: 14px;

    letter-spacing: .08em;

    text-transform: uppercase;
}

.top-content-nav a:hover {

    background: rgba(255,255,255,.05);
}

.top-content-nav a.active {

    background: rgba(255,255,255,.08);

    border-color: rgba(255,255,255,.14);
}

/* ==========================================================================
   STREAM
   ========================================================================== */

.top-content-stream {

    display: flex;

    flex-direction: column;

    gap: 60px;
}

/* ==========================================================================
   ENTRY
   ========================================================================== */

.top-entry {

    display: grid;

    grid-template-columns: 180px 1fr;

    gap: 32px;

    align-items: start;
}

@media (max-width: 800px) {

    .top-entry {

        grid-template-columns: 1fr;
    }
}

/* ==========================================================================
   IMAGE
   ========================================================================== */

.top-entry-image img {

    width: 100%;

    border-radius: 14px;

    display: block;
}

/* ==========================================================================
   CONTENT
   ========================================================================== */

.top-entry-content {

    display: flex;

    flex-direction: column;

    gap: 18px;
}

.top-entry-type {

    font-size: 11px;

    text-transform: uppercase;

    letter-spacing: .14em;

    opacity: .5;
}

.top-entry-title {

    font-size: clamp(26px, 3vw, 40px);

    line-height: 1.1;

    margin: 0;
}

.top-entry-title a {

    text-decoration: none;
}

.top-entry-meta {

    font-size: 15px;

    opacity: .65;
}

.top-entry-text {

    font-size: 20px;

    line-height: 1.9;

    opacity: .92;
}

.top-entry-text p:first-child {

    margin-top: 0;
}

.top-entry-text p:last-child {

    margin-bottom: 0;
}

/* ==========================================================================
   DIVIDER
   ========================================================================== */

.top-entry-divider {

    margin-top: 40px;

    border-top: 1px solid rgba(255,255,255,.08);
}

</style>

<main class="top-content-page">

    <header class="top-content-hero">

        <h1>Top Content</h1>

        <p>
            A curated selection of standout excerpts, quotes, and lyrics
            across the archive.
        </p>

    </header>

    <nav class="top-content-nav">

        <?php foreach ($sections as $key => $section) : ?>

            <a
                href="?view=<?php echo esc_attr($key); ?>"
                class="<?php echo $current === $key ? 'active' : ''; ?>"
            >
                <?php echo esc_html($section['label']); ?>
            </a>

        <?php endforeach; ?>

    </nav>

    <section class="top-content-stream">

        <?php if ($query->have_posts()) : ?>

            <?php while ($query->have_posts()) : $query->the_post();

                $post_id = get_the_ID();

                $image = '';
                $meta  = '';
                $text  = '';

                /*
                |--------------------------------------------------------------------------
                | QUOTES
                |--------------------------------------------------------------------------
                */

                if ($config['post_type'] === 'quote') {

                    $text = get_field('quote_plain_text');

                    $source = get_field('source');

                    if ($source) {

                        $meta = get_the_title($source->ID);

                        $cover = get_field('cover_image', $source->ID);

                        if ($cover && is_array($cover)) {

                            $image =
                                $cover['sizes']['medium']
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
                | EXCERPTS
                |--------------------------------------------------------------------------
                */

                elseif ($config['post_type'] === 'excerpt') {

                    $text = get_field('excerpt_plain_text');

                    $source = get_field('excerpt_source');

                    if ($source) {

                        $meta = get_the_title($source->ID);

                        $cover = get_field('cover_image', $source->ID);

                        if ($cover && is_array($cover)) {

                            $image =
                                $cover['sizes']['medium']
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

                elseif ($config['post_type'] === 'lyric') {

                    $text = get_field('lyric_plain_text');

                    $song = get_field('song');

                    if ($song) {

                        $meta = get_the_title($song->ID);

                        $cover = get_field('cover_image', $song->ID);

                        if ($cover && is_array($cover)) {

                            $image =
                                $cover['sizes']['medium']
                                ?? $cover['url'];

                        } elseif (has_post_thumbnail($song->ID)) {

                            $image = get_the_post_thumbnail_url($song->ID, 'medium');
                        }
                    }

                    if (!$image && has_post_thumbnail($post_id)) {

                        $image = get_the_post_thumbnail_url($post_id, 'medium');
                    }
                }

            ?>

                <article class="top-entry">

                    <?php if ($image) : ?>

                        <div class="top-entry-image">

                            <a href="<?php the_permalink(); ?>">

                                <img
                                    src="<?php echo esc_url($image); ?>"
                                    alt="<?php the_title_attribute(); ?>"
                                >

                            </a>

                        </div>

                    <?php endif; ?>

                    <div class="top-entry-content">

                        <div class="top-entry-type">

                            <?php echo esc_html($config['label']); ?>

                        </div>

                        <h2 class="top-entry-title">

                            <a href="<?php the_permalink(); ?>">

                                <?php the_title(); ?>

                            </a>

                        </h2>

                        <?php if ($meta) : ?>

                            <div class="top-entry-meta">

                                <?php echo esc_html($meta); ?>

                            </div>

                        <?php endif; ?>

                        <?php if ($text) : ?>

                            <div class="top-entry-text">

                                <?php echo wpautop(esc_html($text)); ?>

                            </div>

                        <?php endif; ?>

                        <div class="top-entry-divider"></div>

                    </div>

                </article>

            <?php endwhile; ?>

            <?php wp_reset_postdata(); ?>

        <?php else : ?>

            <p>No curated entries found.</p>

        <?php endif; ?>

    </section>

</main>

<?php get_footer(); ?>