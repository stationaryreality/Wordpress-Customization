<?php
$song_id   = get_the_ID();
$bio       = get_field('song_bio', $song_id);
$cover     = get_field('cover_image', $song_id);
$img_url   = $cover ? $cover['sizes']['medium'] : '';
$wiki_slug = get_field('wikipedia_slug', $song_id);
$artist_profile = get_field('song_artist');

// If the field returns an array, just grab the first item
if (is_array($artist_profile)) {
    $artist_profile = $artist_profile[0];
}

// If the field returns an ID, convert it to a post object
if (is_numeric($artist_profile)) {
    $artist_profile = get_post($artist_profile);
}

// Wikipedia summary
function get_wikipedia_intro($slug) {
  $api_url = "https://en.wikipedia.org/api/rest_v1/page/summary/" . urlencode($slug);
  $response = wp_remote_get($api_url);
  if (is_wp_error($response)) return false;
  $body = wp_remote_retrieve_body($response);
  $data = json_decode($body, true);
  return !empty($data['extract']) ? esc_html($data['extract']) : false;
}
?>

<div class="song-header" style="text-align:center;">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="album-cover" style="border-radius:0; aspect-ratio:1/1; object-fit:cover; max-width:300px; margin-bottom:1em;">
  <?php endif; ?>
  <h1><?php the_title(); ?></h1>
</div>


  <div class="song-bio">
    <?php if ($bio): ?>
      <?php echo wp_kses_post($bio); ?>
    <?php elseif ($wiki_slug): ?>
      <p><?php echo get_wikipedia_intro($wiki_slug); ?></p>
    <?php else: ?>
      <?php the_content(); ?>
    <?php endif; ?>
  </div>


  <?php if ($artist_profile): ?>
  <?php
    $portrait     = get_field('portrait_image', $artist_profile->ID);
    $thumb        = $portrait ? $portrait['sizes']['thumbnail'] : '';
    $bio          = get_field('bio', $artist_profile->ID);
    $profile_slug = get_field('wikipedia_slug', $artist_profile->ID);
  ?>
  <div class="person-content" style="margin-top: 2em;">
    <?php if ($thumb): ?>
      <a href="<?php echo get_permalink($artist_profile->ID); ?>" class="artist-link">
        <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($artist_profile->ID)); ?>" class="author-thumbnail rounded">
        <h3>By <?php echo esc_html(get_the_title($artist_profile->ID)); ?></h3>
      </a>
    <?php else: ?>
      <h3>By <a href="<?php echo get_permalink($artist_profile->ID); ?>">
        <?php echo esc_html(get_the_title($artist_profile->ID)); ?>
      </a></h3>
    <?php endif; ?>
  </div>
<?php endif; ?>


  <?php
  // === Chapters Featuring This Song ===
  $chapters = get_posts([
    'post_type'      => 'chapter',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'meta_query'     => [
      'relation' => 'OR',
      [
        'key'     => 'primary_song',
        'value'   => $song_id,
        'compare' => '='
      ],
      [
        'key'     => 'secondary_song',
        'value'   => $song_id,
        'compare' => '='
      ],
      [
        'key'     => 'tertiary_song',
        'value'   => $song_id,
        'compare' => '='
      ],
      [
        'key'     => 'quaternary_song',
        'value'   => $song_id,
        'compare' => '='
      ]
    ]
  ]);

$youtube_url = get_field('youtube_url');
if ($youtube_url) {
  $embed_html = wp_oembed_get($youtube_url);

  // Gutenberg-style wrapper with centering
  echo '<figure class="wp-block-embed is-type-video" style="text-align:center;margin:2em auto;">';
  echo '<div class="wp-block-embed__wrapper" style="display:inline-block;">';
  echo $embed_html;
  echo '</div>';
  echo '</figure>';
}


// === Isolated Lyrics (on song CPT itself) ===
$isolated_lyrics = get_field('isolated_lyrics');
if (!empty($isolated_lyrics)) {
  echo '<div class="isolated-lyrics">';
  echo '<h2>Song Excerpts</h2>';
  foreach ($isolated_lyrics as $lyric) {
    $html = $lyric['lyric_html_block'] ?? '';
    $text = $lyric['quote_text'] ?? '';
    if ($html) {
      echo $html;
    } elseif ($text) {
      echo '<div class="plain-lyric"><blockquote>' . esc_html($text) . '</blockquote></div>';
    }
  }
  echo '</div>';
}

  if ($chapters): ?>
    <div class="narrative-threads">
      <h2>Narrative Threads Featuring This Song</h2>
      <div class="thread-grid">
        <?php foreach ($chapters as $chapter):
          $thumb = get_the_post_thumbnail_url($chapter->ID, 'medium');
        ?>
          <div class="thread-item">
            <a href="<?php echo get_permalink($chapter->ID); ?>">
              <?php if ($thumb): ?>
                <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($chapter->ID)); ?>">
              <?php endif; ?>
              <h3><?php echo esc_html(get_the_title($chapter->ID)); ?></h3>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <?php
  // === Song Excerpts ===
  $lyrics = get_posts([
    'post_type'      => 'lyric',
    'posts_per_page' => -1,
    'meta_query'     => [
      [
        'key'     => 'song',
        'value'   => $song_id,
        'compare' => '='
      ]
    ]
  ]);

  if ($lyrics): ?>
    <div class="artist-lyrics">
      <h2>Song Excerpts</h2>
      <?php foreach ($lyrics as $lyric):
        $html = get_field('lyric_html_block', $lyric->ID);
        $text = get_field('quote_text', $lyric->ID);
        if ($html) {
          echo $html;
        } elseif ($text) {
          echo '<div class="plain-lyric"><blockquote>' . esc_html($text) . '</blockquote></div>';
        }
      endforeach; ?>
    </div>
  <?php endif; ?>

  <?php get_template_part('content/song-nav'); ?>
</div>
