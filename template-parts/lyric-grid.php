<?php
/**
 * Unified Lyric Display (passive)
 *
 * Expects:
 * - query => WP_Query or array of posts
 * - title => Section title (optional)
 * - emoji => Optional
 */

$query = $args['query'] ?? null;
$title = $args['title'] ?? 'Lyrics';
$emoji = $args['emoji'] ?? '';

if (!$query) return;

// Normalize
$posts = $query instanceof WP_Query ? $query->posts : $query;
if (empty($posts)) return;
?>

<section class="portal-section lyric-list-section">
  <?php if ($title): ?>
    <h2 class="portal-section-title">
      <?php if ($emoji) echo '<span class="emoji">' . esc_html($emoji) . '</span> '; ?>
      <?php echo esc_html($title); ?>
    </h2>
  <?php endif; ?>

  <div class="portal-lyric-list">
    <?php foreach ($posts as $post_obj):
      $post_id = is_object($post_obj) ? $post_obj->ID : intval($post_obj);

      $text  = get_field('lyric_plain_text', $post_id);
      $song  = get_field('song', $post_id);
      $song_link  = $song ? get_permalink($song->ID) : '';
      $song_title = $song ? get_the_title($song->ID) : '';

      // Get artist from song (ACF relationship)
      $artist_name = '';
      $artist_link = '';
      if ($song) {
        $artist = get_field('song_artist', $song->ID);
        if ($artist) {
          // Handle both array and single object cases
          if (is_array($artist)) {
            $artist = reset($artist);
          }
          $artist_name = get_the_title($artist->ID);
          $artist_link = get_permalink($artist->ID);
        }
      }

      // Image: song cover preferred, fallback post thumbnail
      $image = '';
      if ($song) {
        $cover = get_field('cover_image', $song->ID);
        if ($cover && is_array($cover)) {
          $image = $cover['sizes']['medium'] ?? ($cover['sizes']['thumbnail'] ?? ($cover['url'] ?? ''));
        } elseif (has_post_thumbnail($song->ID)) {
          $image = get_the_post_thumbnail_url($song->ID, 'medium');
        }
      }
      if (!$image && has_post_thumbnail($post_id)) {
        $image = get_the_post_thumbnail_url($post_id, 'medium');
      }
    ?>
      <article class="portal-lyric-item">
        <?php if ($image): ?>
          <div class="lyric-thumb">
            <a href="<?php echo esc_url($song_link ?: get_permalink($post_id)); ?>">
              <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($song_title ?: get_the_title($post_id)); ?>">
            </a>
          </div>
        <?php endif; ?>

        <div class="lyric-content">
          <h3 class="lyric-title">
            <a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html(get_the_title($post_id)); ?></a>
          </h3>

          <?php if ($text): ?>
            <p class="lyric-snippet">
              <?php echo esc_html(wp_trim_words($text, 80, '...')); ?>
            </p>
          <?php endif; ?>

          <?php if ($song): ?>
            <p class="lyric-source">
              Source: <a href="<?php echo esc_url($song_link); ?>"><?php echo esc_html($song_title); ?></a>
              <?php if ($artist_name): ?>
                &nbsp;by <a href="<?php echo esc_url($artist_link); ?>"><?php echo esc_html($artist_name); ?></a>
              <?php endif; ?>
            </p>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<?php if ($query instanceof WP_Query) wp_reset_postdata(); ?>
