<?php
$song_id        = get_the_ID();
$cover          = get_field('cover_image', $song_id);
$img_url        = $cover ? $cover['sizes']['medium'] : '';
$artist_profile = get_field('song_artist', $song_id);
$youtube_url    = get_field('youtube_url', $song_id);
?>

<div class="person-content cpt-song-content">

    <?php get_template_part('template-parts/navigation/song'); ?>

    <?php if ($img_url): ?>
        <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="cpt-song-cover">
    <?php endif; ?>

    <h1 class="cpt-song-title"><?php the_title(); ?></h1>

    <div class="cpt-song-bio">
        <?php the_content(); ?>
    </div>

    <?php if ($artist_profile): ?>
        <?php
        $portrait = get_field('portrait_image', $artist_profile->ID);
        $thumb    = $portrait ? $portrait['sizes']['thumbnail'] : '';
        ?>
        <div class="cpt-song-artist">
            <a href="<?php echo esc_url(get_permalink($artist_profile->ID)); ?>" class="cpt-song-artist-link">
                <?php if ($thumb): ?>
                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($artist_profile->ID)); ?>" class="cpt-song-artist-thumb">
                <?php endif; ?>
                <h3 class="cpt-song-artist-name"><?php echo esc_html(get_the_title($artist_profile->ID)); ?></h3>
            </a>
        </div>
    <?php endif; ?>

    <?php
    $lyrics = get_posts([
        'post_type'      => 'lyric',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => [['key' => 'song', 'value' => $song_id, 'compare' => '=']]
    ]);
    if (!empty($lyrics)) {
        get_template_part('template-parts/render/content-objects', null, ['posts' => $lyrics, 'title' => 'Song Excerpts']);
    }
    ?>

    <?php if ($youtube_url): ?>
        <figure class="cpt-song-video">
            <div class="cpt-song-video-wrapper">
                <?php echo wp_oembed_get($youtube_url); ?>
            </div>
        </figure>
    <?php endif; ?>

    <?php
    $roles = kp_get_song_thread_roles($song_id);
    $featured_chapters = array_merge($roles['chapter']['primary'], $roles['chapter']['secondary']);
    $featured_fragments = array_merge($roles['fragment']['primary'], $roles['fragment']['secondary']);
    $referenced_in = array_merge($roles['chapter']['supporting'], $roles['fragment']['supporting']);

    get_template_part('template-parts/views/featured-in-grid', null, ['title' => 'Narrative Threads', 'items' => $featured_chapters]);
    get_template_part('template-parts/views/featured-in-grid', null, ['title' => 'Narrative Fragments', 'items' => $featured_fragments]);

    if (!empty($referenced_in)): ?>
        <div class="cpt-song-referenced">
            <h2 class="cpt-song-referenced-title">Referenced In</h2>
            <div class="cpt-song-referenced-grid">
                <?php foreach ($referenced_in as $item):
                    $thumb = get_the_post_thumbnail_url($item->ID, 'medium');
                ?>
                    <a href="<?php echo esc_url(get_permalink($item->ID)); ?>" class="cpt-song-referenced-item">
                        <?php if ($thumb): ?>
                            <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($item->ID)); ?>" class="cpt-song-referenced-image">
                        <?php endif; ?>
                        <h3 class="cpt-song-referenced-name"><?php echo esc_html(get_the_title($item->ID)); ?></h3>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</div>