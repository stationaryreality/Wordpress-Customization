<?php
// content-video.php

$video_id      = get_the_ID();

$parent_song   = get_field('video_parent_song');
$video_creator = get_field('video_creator');

$video_image   = get_field('video_screenshot');
$video_img_url = $video_image ? $video_image['sizes']['large'] : '';

$video_url     = get_field('video_url');
$video_caption = get_field('video_caption');

// =====================================================
// INHERIT FROM PARENT SONG
// =====================================================

$artist_profile = null;
$song_title     = '';
$song_cover     = '';
$youtube_url    = '';

if ($parent_song) {

    $artist_profile = get_field('song_artist', $parent_song->ID);

    $song_title = get_the_title($parent_song->ID);

    $cover = get_field('cover_image', $parent_song->ID);

    $song_cover = $cover
        ? $cover['sizes']['thumbnail']
        : '';

    $youtube_url = get_field('youtube_url', $parent_song->ID);
}

// =====================================================
// FALLBACK TO VIDEO CREATOR
// =====================================================

if (!$artist_profile && $video_creator) {
    $artist_profile = $video_creator;
}

// =====================================================
// VIDEO URL OVERRIDE
// =====================================================

if ($video_url) {
    $youtube_url = $video_url;
}
?>

<div class="video-header" style="text-align:center;">


    <h1><?php the_title(); ?></h1>

</div>

<div class="video-bio" style="text-align:center; max-width:900px; margin:0 auto;">

    <?php the_content(); ?>

</div>

<?php if ($artist_profile): ?>

    <?php
    $portrait = get_field('portrait_image', $artist_profile->ID);

    $thumb = $portrait
        ? $portrait['sizes']['thumbnail']
        : '';
    ?>

    <div class="person-content" style="margin-top:2em; text-align:center;">

        <a href="<?php echo get_permalink($artist_profile->ID); ?>"
           class="artist-link">

            <?php if ($thumb): ?>

                <img
                    src="<?php echo esc_url($thumb); ?>"
                    alt="<?php echo esc_attr(get_the_title($artist_profile->ID)); ?>"
                    class="author-thumbnail rounded"
                >

            <?php endif; ?>

            
            <h3>
                <?php echo esc_html(get_the_title($artist_profile->ID)); ?>
            </h3>

        </a>

    </div>

<?php endif; ?>

<?php
// =====================================================
// YOUTUBE EMBED
// =====================================================

if ($youtube_url) {

    $embed_html = wp_oembed_get($youtube_url);

    echo '<figure class="wp-block-embed is-type-video"
             style="
                text-align:center;
                margin:3em auto;
             ">';

    echo '<div class="wp-block-embed__wrapper"
             style="
                display:inline-block;
                max-width:100%;
             ">';

    echo $embed_html;

    echo '</div>';

    // ==========================================
    // OPTIONAL PARENT SONG REFERENCE
    // ==========================================

    if ($parent_song) {

        echo '<div class="video-parent-song"
                 style="
                    margin-top:1em;
                    text-align:center;
                 ">';

        if ($song_cover) {

            echo '<a href="' . get_permalink($parent_song->ID) . '"
                     style="
                        text-decoration:none;
                        color:inherit;
                     ">';

            echo '<img
                    src="' . esc_url($song_cover) . '"
                    alt="' . esc_attr($song_title) . '"
                    style="
                        width:70px;
                        height:70px;
                        object-fit:cover;
                        margin:0 auto 0.5em;
                        display:block;
                    "
                  >';

            echo '<div style="font-size:0.95rem;">'
                    . esc_html($song_title) .
                 '</div>';

            echo '</a>';
        }

        echo '</div>';
    }

    echo '</figure>';
}
?>

<?php
// =====================================================
// OPTIONAL LYRICS (FROM PARENT SONG)
// =====================================================

if ($parent_song) {

    $lyrics = get_posts([
        'post_type'      => 'lyric',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',

        'meta_query' => [
            [
                'key'     => 'song',
                'value'   => $parent_song->ID,
                'compare' => '='
            ]
        ]
    ]);

    if ($lyrics):
?>

    <div class="artist-lyrics"
         style="margin-top:3em; text-align:center;">

        <h2>Song Excerpts</h2>

        <ul style="
            list-style:none;
            padding:0;
            display:inline-block;
            text-align:center;
        ">

            <?php foreach ($lyrics as $lyric): ?>

                <li>

                    <a href="<?php echo get_permalink($lyric->ID); ?>">

                        <?php echo esc_html(get_the_title($lyric->ID)); ?>

                    </a>

                </li>

            <?php endforeach; ?>

        </ul>

    </div>

<?php
    endif;
}
?>


<?php
// =====================================================
// VIDEO CPT RELATIONSHIPS
// =====================================================

show_featured_in_threads('videos_linked');
?>

<div style="text-align:center; margin-top:2em;">
    <?php echo fn_taxonomy_bubbles(get_the_ID()); ?>
</div>

<?php get_template_part('content/video-nav'); ?>