<?php
$video_id      = get_the_ID();
$parent_song   = get_field('video_parent_song', $video_id);
$youtube_url   = get_field('video_url', $video_id);

// If no YouTube URL is set on the video itself, try to get it from the parent song
if (!$youtube_url && $parent_song) {
    $youtube_url = get_field('youtube_url', $parent_song->ID);
}
?>

<div class="person-content cpt-video-content">

    <?php get_template_part('template-parts/navigation/video'); ?>

    <h1 class="cpt-video-title"><?php the_title(); ?></h1>

    <?php if ($youtube_url): ?>
        <figure class="cpt-video-embed">
            <div class="cpt-video-embed-wrapper">
                <?php echo wp_oembed_get($youtube_url); ?>
            </div>
        </figure>
    <?php endif; ?>

    <div class="cpt-video-bio">
        <?php the_content(); ?>
    </div>

    <?php if ($parent_song): ?>
        <div class="cpt-video-parent-cta">
            <p>This video is part of a larger song entry.</p>
            <a href="<?php echo esc_url(get_permalink($parent_song->ID)); ?>" class="cpt-video-parent-button">
                View Song Page: <?php echo esc_html(get_the_title($parent_song->ID)); ?> →
            </a>
        </div>
    <?php endif; ?>

    <?php show_featured_in_threads('videos_linked'); ?>
    
    <div class="cpt-video-bubbles">
        <?php echo fn_taxonomy_bubbles($video_id); ?>
    </div>

</div>