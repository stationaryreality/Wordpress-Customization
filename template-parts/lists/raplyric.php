<?php
$query       = $args['query'] ?? null;
$items       = $args['items'] ?? [];
$info        = $args['info'] ?? [];
$search_term = $args['search_term'] ?? '';

$title = $info['title'] ?? $args['title'] ?? 'Rap Lyrics';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '✍️';

if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $id = get_the_ID();

        $song = get_field('song', $id);
        if (is_array($song)) $song = reset($song);
        if (is_numeric($song)) $song = get_post((int)$song);

        $img = '';
        if ($song instanceof WP_Post) {
            $cover = get_field('cover_image', $song->ID);
            if (is_array($cover)) $img = $cover['sizes']['thumbnail'] ?? $cover['url'] ?? '';
            elseif (is_numeric($cover)) $img = wp_get_attachment_image_url($cover, 'thumbnail');
        }

        $text = get_field('lyric_plain_text', $id) ?: get_the_excerpt();

        $items[] = ['id' => $id, 'title' => get_the_title(), 'url' => get_permalink(), 'image' => $img, 'excerpt' => $text];
    }
    wp_reset_postdata();
}

if (empty($items)) return;
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
            <article class="cpt-lyric-item">
                <?php if (!empty($item['image'])): ?>
                    <div class="cpt-lyric-thumb">
                        <a href="<?php echo esc_url($item['url']); ?>">
                            <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>">
                        </a>
                    </div>
                <?php endif; ?>
                <div class="cpt-lyric-details">
                    <h3 class="cpt-lyric-card-title">
                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                    </h3>
                    <?php if (!empty($item['excerpt'])): ?>
                        <p class="cpt-lyric-snippet"><?php echo esc_html(wp_trim_words($item['excerpt'], 80, '...')); ?></p>
                    <?php endif; ?>
                    <p class="cpt-lyric-source"><?php echo kp_lyric_attribution($item['id']); ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>