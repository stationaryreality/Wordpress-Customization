<?php
/**
 * Unified Lyric Display (passive)
 *
 * Standardized contract:
 * - items       => array (normalized cards)
 * - query       => WP_Query|null (optional, for legacy callers)
 * - info        => [ 'title' => '', 'emoji' => '', 'type' => '' ]
 * - search_term => string (optional)
 */
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$info         = $args['info'] ?? [];
$search_term  = $args['search_term'] ?? '';

// Backward compatibility: allow direct title/emoji from older callers
$title = $info['title'] ?? $args['title'] ?? 'Lyrics';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '';

// If a WP_Query was passed, convert it to cards (legacy support)
if ($query instanceof WP_Query && $query->have_posts()) {
    $items = [];
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = kp_build_card('lyric', get_the_ID(), get_cpt_metadata());
    }
    wp_reset_postdata();
}

// No data to render
if (empty($items)) {
    return;
}
?>

<section class="portal-section lyric-list-section">
  <?php if ($title): ?>
    <h2 class="portal-section-title">
      <?php if ($emoji) echo '<span class="emoji">' . esc_html($emoji) . '</span> '; ?>
      <?php echo esc_html($title); ?>
    </h2>
  <?php endif; ?>

  <div class="portal-lyric-list">
    <?php foreach ($items as $item): ?>
      <?php
        $image = !empty($item['image']) ? $item['image'] : '';
        $text  = !empty($item['excerpt']) ? $item['excerpt'] : '';
        $meta  = !empty($item['meta']) && is_array($item['meta']) ? $item['meta'] : [];
        $song_title = $meta['song_title'] ?? '';
        $song_url   = $meta['song_url'] ?? '';
        $artist_name = $meta['artist_name'] ?? '';
        $artist_url  = $meta['artist_url'] ?? '';
      ?>
      <article class="portal-lyric-item">
        <?php if ($image): ?>
          <div class="lyric-thumb">
            <a href="<?php echo esc_url($song_url ?: $item['url']); ?>">
              <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($song_title ?: $item['title']); ?>">
            </a>
          </div>
        <?php endif; ?>

        <div class="lyric-content">
          <h3 class="lyric-title">
            <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
          </h3>

          <?php if ($text): ?>
            <p class="lyric-snippet">
              <?php echo esc_html(wp_trim_words($text, 80, '...')); ?>
            </p>
          <?php endif; ?>

          <?php if ($song_title && $song_url): ?>
            <p class="lyric-source">
              Source: <a href="<?php echo esc_url($song_url); ?>"><?php echo esc_html($song_title); ?></a>
              <?php if ($artist_name && $artist_url): ?>
                &nbsp;by <a href="<?php echo esc_url($artist_url); ?>"><?php echo esc_html($artist_name); ?></a>
              <?php endif; ?>
            </p>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>