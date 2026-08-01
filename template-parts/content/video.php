<?php
$video_id      = get_the_ID();
$parent_song   = get_field('video_parent_song', $video_id);
$video_creator = get_field('video_creator', $video_id);
$video_image   = get_field('video_screenshot', $video_id);
$video_url     = get_field('video_url', $video_id);

// =====================================================
// INHERIT FROM PARENT SONG
// =====================================================
$artist_profile = null;
$song_title     = '';
$song_cover     = '';
$youtube_url    = '';

if ($parent_song) {
    $artist_profile = get_field('song_artist', $parent_song->ID);
    $song_title     = get_the_title($parent_song->ID);
    $cover          = get_field('cover_image', $parent_song->ID);
    $song_cover     = $cover ? $cover['sizes']['thumbnail'] : '';
    $youtube_url    = get_field('youtube_url', $parent_song->ID);
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

<div class="person-content cpt-video-content">

    <?php get_template_part('template-parts/navigation/video'); ?>

    <h1 class="cpt-video-title"><?php the_title(); ?></h1>

    <div class="cpt-video-bio">
        <?php the_content(); ?>
    </div>

    <?php if ($artist_profile): ?>
        <?php
        $portrait = get_field('portrait_image', $artist_profile->ID);
        $thumb    = $portrait ? $portrait['sizes']['thumbnail'] : '';
        ?>
        <div class="cpt-video-artist">
            <a href="<?php echo esc_url(get_permalink($artist_profile->ID)); ?>" class="cpt-video-artist-link">
                <?php if ($thumb): ?>
                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($artist_profile->ID)); ?>" class="cpt-video-artist-thumb">
                <?php endif; ?>
                <h3 class="cpt-video-artist-name"><?php echo esc_html(get_the_title($artist_profile->ID)); ?></h3>
            </a>
        </div>
    <?php endif; ?>

    <?php if ($youtube_url): ?>
        <figure class="cpt-video-embed">
            <div class="cpt-video-embed-wrapper">
                <?php echo wp_oembed_get($youtube_url); ?>
            </div>
            
            <?php if ($parent_song && $song_cover): ?>
                <div class="cpt-video-parent-song">
                    <a href="<?php echo esc_url(get_permalink($parent_song->ID)); ?>" class="cpt-video-parent-link">
                        <img src="<?php echo esc_url($song_cover); ?>" alt="<?php echo esc_attr($song_title); ?>" class="cpt-video-parent-thumb">
                        <span class="cpt-video-parent-title"><?php echo esc_html($song_title); ?></span>
                    </a>
                </div>
            <?php endif; ?>
        </figure>
    <?php endif; ?>

    <?php if ($parent_song): ?>
        <?php
        $lyrics = get_posts([
            'post_type'      => 'lyric',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'meta_query'     => [['key' => 'song', 'value' => $parent_song->ID, 'compare' => '=']]
        ]);

        if ($lyrics): ?>
            <div class="cpt-video-lyrics">
                <h2 class="cpt-video-lyrics-title">Song Excerpts</h2>
                <ul class="cpt-video-lyrics-list">
                    <?php foreach ($lyrics as $lyric): ?>
                        <li class="cpt-video-lyrics-item">
                            <a href="<?php echo esc_url(get_permalink($lyric->ID)); ?>">
                                <?php echo esc_html(get_the_title($lyric->ID)); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php show_featured_in_threads('videos_linked'); ?>

    <div class="cpt-video-bubbles">
        <?php echo fn_taxonomy_bubbles($video_id); ?>
    </div>

</div>