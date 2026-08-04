<?php
/**
 * Unified Lyric Display
 */
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$info         = $args['info'] ?? [];
$search_term  = $args['search_term'] ?? '';

$title = $info['title'] ?? $args['title'] ?? 'Lyrics';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '🎵';

if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = kp_build_card('lyric', get_the_ID(), get_cpt_metadata());
    }
    wp_reset_postdata();
}

if (empty($items)) {
    return;
}
?>

<section class="cpt-lyric-section">
    <h2 class="cpt-lyric-section-title">
        <?php if ($emoji) echo '<span class="emoji">' . esc_html($emoji) . '</span> '; ?>
        <?php echo esc_html($title); ?>
        <?php if ($search_term): ?>
            <span>containing “<?php echo esc_html($search_term); ?>”</span>
        <?php endif; ?>
    </h2>

    <div class="cpt-lyric-list">
        <?php foreach ($items as $item): ?>
            <?php
            $image = !empty($item['image']) ? $item['image'] : '';
            $text  = !empty($item['excerpt']) ? $item['excerpt'] : '';
            
            // Restore explicit array extraction for song/artist links
            $meta = !empty($item['meta']) && is_array($item['meta']) ? $item['meta'] : [];
            $song_title  = $meta['song_title'] ?? '';
            $song_url    = $meta['song_url'] ?? '';
            $artist_name = $meta['artist_name'] ?? '';
            $artist_url  = $meta['artist_url'] ?? '';
            
            // Fallback if meta is already a pre-formatted HTML string
            $meta_html = (!empty($item['meta']) && is_string($item['meta'])) ? $item['meta'] : '';
            ?>
            
            <article class="cpt-lyric-item">
                <?php if ($image): ?>
                    <div class="cpt-lyric-thumb">
                        <a href="<?php echo esc_url($song_url ?: $item['url']); ?>">
                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($song_title ?: $item['title']); ?>">
                        </a>
                    </div>
                <?php endif; ?>

                <div class="cpt-lyric-details">
                    <h3 class="cpt-lyric-card-title">
                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                    </h3>

                    <?php if ($text): ?>
                        <p class="cpt-lyric-snippet">
                            <?php echo esc_html(wp_trim_words($text, 80, '...')); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($song_title && $song_url): ?>
                        <p class="cpt-lyric-source">
                            Source: <a href="<?php echo esc_url($song_url); ?>"><?php echo esc_html($song_title); ?></a>
                            <?php if ($artist_name && $artist_url): ?>
                                &nbsp;by <a href="<?php echo esc_url($artist_url); ?>"><?php echo esc_html($artist_name); ?></a>
                            <?php endif; ?>
                        </p>
                    <?php elseif ($meta_html): ?>
                        <p class="cpt-lyric-meta">
                            <?php echo wp_kses_post($meta_html); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>