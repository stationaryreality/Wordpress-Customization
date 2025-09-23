<?php
/**
 * Shared Lyric Grid Template
 *
 * Expects:
 * - query       => WP_Query object
 * - title       => Section/page title
 * - emoji       => Emoji (optional, from centralized lookup)
 * - search_term => Optional (only used on search)
 */
$query       = $args['query'];
$title       = $args['title'] ?? 'Lyrics';
$emoji       = $args['emoji'] ?? '';
$search_term = $args['search_term'] ?? '';

if (!$query->have_posts()) return;
?>

<section class="lyric-grid container" style="max-width: 800px; margin: auto; padding: 2rem 1rem;">
  <h1>
    <?php if ($emoji) echo $emoji . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term) : ?>
      containing “<?php echo esc_html($search_term); ?>”
    <?php endif; ?>
  </h1>
  <p class="intro-text">Lyrics referenced across featured chapters and profiles.</p>

  <div class="lyric-list">
    <?php while ($query->have_posts()) : $query->the_post(); ?>
      <?php
        $text  = get_field('lyric_plain_text', get_the_ID());
        $song  = get_field('song', get_the_ID());
        $source_link  = $song ? get_permalink($song->ID) : '';
        $source_title = $song ? get_the_title($song->ID) : '';

        // Song cover image
        $image = '';
        if ($song) {
          $cover = get_field('cover_image', $song->ID);
          if ($cover) {
            $image = $cover['sizes']['thumbnail'];
          } elseif (has_post_thumbnail($song->ID)) {
            $image = get_the_post_thumbnail_url($song->ID, 'thumbnail');
          }
        }
      ?>
      <div class="lyric-entry" style="display:flex; align-items:flex-start; gap:1rem; margin-bottom:2rem; border-bottom:1px solid #ddd; padding-bottom:1rem;">
        <?php if ($image): ?>
          <a href="<?php echo esc_url($source_link); ?>" class="lyric-thumb">
            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($source_title); ?>" style="width:48px; height:48px; border-radius:50%; object-fit:cover;">
          </a>
        <?php endif; ?>

        <div class="lyric-text">
          <h2 style="margin-bottom:0.5rem;">
            <a href="<?php the_permalink(); ?>">
              <?php the_title(); ?>
            </a>
          </h2>

          <?php if ($text): ?>
            <?php 
              // Normalize line endings to \n
              $normalized_text = str_replace(["\r\n", "\r"], "\n", $text);
              $lines = explode("\n", $normalized_text);
              $first_line = '';
              foreach ($lines as $line) {
                  if (trim($line) !== '') {
                      $first_line = $line;
                      break;
                  }
              }
            ?>
            <p style="margin:0;"><?php echo esc_html($first_line); ?><?php if (count($lines) > 1) echo '...'; ?></p>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</section>
<?php wp_reset_postdata(); ?>
