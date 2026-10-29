<?php
$rap_id             = get_the_ID();
$cover              = get_field('cover_image', $rap_id);
$img_url            = $cover ? $cover['sizes']['medium'] : '';
$artist_profile     = get_field('song_artist', $rap_id);
$additional_artists = get_field('additional_artists', $rap_id);
$also_featuring     = get_field('also_featuring', $rap_id);
$youtube_url        = get_field('youtube_url', $rap_id);
?>

<div class="person-content cpt-song-content">

    <?php get_template_part('template-parts/navigation/rap'); ?>

    <?php if ($img_url): ?>
        <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="cpt-song-cover">
    <?php endif; ?>

    <h1 class="cpt-song-title"><?php the_title(); ?></h1>

    <div class="cpt-song-bio">
        <?php the_content(); ?>
    </div>

    <?php if ($artist_profile): ?>
        <div class="cpt-song-artist-primary">
            <?php
            $portrait = get_field('portrait_image', $artist_profile->ID);
            $thumb    = $portrait ? $portrait['sizes']['thumbnail'] : '';
            ?>
            <a href="<?php echo esc_url(get_permalink($artist_profile->ID)); ?>" class="cpt-song-artist-node">
                <?php if ($thumb): ?>
                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($artist_profile->ID)); ?>" class="cpt-song-artist-thumb">
                <?php endif; ?>
                <span class="cpt-song-artist-name"><?php echo esc_html(get_the_title($artist_profile->ID)); ?></span>
            </a>
        </div>

        <?php if (!empty($additional_artists)): ?>
            <div class="cpt-song-featuring">
                <span class="cpt-song-featuring-label">featuring</span>
                <div class="cpt-song-featuring-row">
                    <?php foreach ($additional_artists as $add_artist): ?>
                        <?php
                        $add_portrait = get_field('portrait_image', $add_artist->ID);
                        $add_thumb    = $add_portrait ? $add_portrait['sizes']['thumbnail'] : '';
                        ?>
                        <a href="<?php echo esc_url(get_permalink($add_artist->ID)); ?>" class="cpt-song-artist-node">
                            <?php if ($add_thumb): ?>
                                <img src="<?php echo esc_url($add_thumb); ?>" alt="<?php echo esc_attr(get_the_title($add_artist->ID)); ?>" class="cpt-song-featuring-thumb">
                            <?php endif; ?>
                            <span class="cpt-song-artist-name"><?php echo esc_html(get_the_title($add_artist->ID)); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($also_featuring)): ?>
            <div class="cpt-song-also-featuring">
                <span class="cpt-song-also-featuring-label">also featuring</span>
                <span class="cpt-song-also-featuring-names"><?php echo esc_html($also_featuring); ?></span>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php
    // UPDATED: Query the new raplyric CPT
    $lyrics = get_posts([
        'post_type'      => 'raplyric',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => [['key' => 'song', 'value' => $rap_id, 'compare' => '=']]
    ]);
    
    if (!empty($lyrics)) {
        // UPDATED: Changed title to "Rap Excerpts"
        get_template_part('template-parts/render/content-objects', null, ['posts' => $lyrics, 'title' => 'Rap Excerpts']);
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
    // NOTE: Ensure kp_get_song_thread_roles() accepts 'rap' post IDs if it has internal type checks
    $roles = kp_get_song_thread_roles($rap_id);
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