<?php
/**
 * Template Name: Lyrics Directory
 */
get_header();

$lyrics = get_posts([
  'post_type'   => 'lyric',
  'numberposts' => -1,
  'orderby'     => 'title',
  'order'       => 'ASC',
]);
?>

<main class="lyrics-directory">
  <section class="container" style="max-width: 800px; margin: auto; padding: 2rem 1rem;">
    <h1>Lyrics</h1>
    <p class="intro-text">Lyrics referenced across featured chapters and profiles.</p>

    <div class="lyric-list">
      <?php foreach ($lyrics as $lyric): ?>
        <?php
          $text  = get_field('lyric_plain_text', $lyric->ID);
          $song  = get_field('song', $lyric->ID);
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
              <a href="<?php echo get_permalink($lyric); ?>">
                <?php echo esc_html(get_the_title($lyric)); ?>
              </a>
            </h2>

            <?php if ($text): ?>
              <p style="margin:0;"><?php echo esc_html(wp_trim_words($text, 30, '...')); ?></p>
            <?php endif; ?>

            <?php if ($song): ?>
<p style="margin-top:0.5rem; font-size:0.9rem; color:#666;">
                Source: <a href="<?php echo esc_url($source_link); ?>"><?php echo esc_html($source_title); ?></a>
              </p>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php get_footer(); ?>
