<?php
$song_id   = get_the_ID();
$cover     = get_field('cover_image', $song_id);
$img_url   = $cover ? $cover['sizes']['medium'] : '';
$artist_profile = get_field('song_artist');
?>

<div class="song-header" style="text-align:center;">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="album-cover" style="border-radius:0; aspect-ratio:1/1; object-fit:cover; max-width:300px; margin-bottom:1em;">
  <?php endif; ?>
  <h1><?php the_title(); ?></h1>
</div>

<div class="song-bio">
  <?php the_content(); ?>
</div>

<?php if ($artist_profile): ?>
  <?php
    $portrait = get_field('portrait_image', $artist_profile->ID);
    $thumb    = $portrait ? $portrait['sizes']['thumbnail'] : '';
  ?>
  <div class="person-content" style="margin-top: 2em;">
    <a href="<?php echo get_permalink($artist_profile->ID); ?>" class="artist-link">
      <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($artist_profile->ID)); ?>" class="author-thumbnail rounded">
      <h3><?php echo esc_html(get_the_title($artist_profile->ID)); ?></h3>
    </a>
  </div>
<?php endif; ?>


<?php
// === Lyrics CPTs (linked to this song) ===
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
  <div class="artist-lyrics" style="margin-top:3em; text-align:center;">
    <h2>Song Excerpts</h2>
    <ul style="list-style:none; padding:0; display:inline-block; text-align:center;">
      <?php foreach ($lyrics as $lyric): ?>
        <li>
          <a href="<?php echo get_permalink($lyric->ID); ?>">
            <?php echo esc_html(get_the_title($lyric->ID)); ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>


<?php
// === YouTube Embed ===
$youtube_url = get_field('youtube_url');
if ($youtube_url) {
  $embed_html = wp_oembed_get($youtube_url);
  echo '<figure class="wp-block-embed is-type-video" style="text-align:center;margin:2em auto;">';
  echo '<div class="wp-block-embed__wrapper" style="display:inline-block;">';
  echo $embed_html;
  echo '</div>';
  echo '</figure>';
}


// === Helper to collect songs for both CPTs ===
function collect_song_roles($post_type, $song_id) {
  $posts = get_posts([
    'post_type'      => $post_type,
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
  ]);

  $roles = ['primary' => [], 'secondary' => [], 'supporting' => []];

  foreach ($posts as $post) {
    $songs = get_field("chapter_songs", $post->ID); // same field key used in both CPTs
    if ($songs) {
      foreach ($songs as $row) {
        if (!empty($row['song']->ID) && $row['song']->ID == $song_id) {
          if ($row['role'] === 'primary') {
            $roles['primary'][] = $post;
          } elseif ($row['role'] === 'secondary') {
            $roles['secondary'][] = $post;
          } else {
            $roles['supporting'][] = $post;
          }
        }
      }
    }
  }
  return $roles;
}

$chapter_roles  = collect_song_roles('chapter', $song_id);
$fragment_roles = collect_song_roles('fragment', $song_id);
?>


<?php if (!empty($chapter_roles['primary']) || !empty($chapter_roles['secondary'])): ?>
  <div class="narrative-threads" style="margin-top: 4em; text-align:center;">
    <h2>Narrative Thread</h2>
    <div class="thread-grid">
      <?php foreach (array_merge($chapter_roles['primary'], $chapter_roles['secondary']) as $chapter):
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


<?php if (!empty($fragment_roles['primary']) || !empty($fragment_roles['secondary'])): ?>
  <div class="narrative-fragments" style="margin-top: 4em; text-align:center;">
    <h2>Narrative Fragment</h2>
    <div class="thread-grid">
      <?php foreach (array_merge($fragment_roles['primary'], $fragment_roles['secondary']) as $fragment):
        $thumb = get_the_post_thumbnail_url($fragment->ID, 'medium');
      ?>
        <div class="thread-item">
          <a href="<?php echo get_permalink($fragment->ID); ?>">
            <?php if ($thumb): ?>
              <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($fragment->ID)); ?>">
            <?php endif; ?>
            <h3><?php echo esc_html(get_the_title($fragment->ID)); ?></h3>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>


<?php if (!empty($chapter_roles['supporting']) || !empty($fragment_roles['supporting'])): ?>
  <div class="narrative-supporting" style="margin-top: 4em; text-align:center;">
    <h2>Referenced In</h2>
    <div class="thread-grid small-grid">
      <?php foreach (array_merge($chapter_roles['supporting'], $fragment_roles['supporting']) as $item):
        $thumb = get_the_post_thumbnail_url($item->ID, 'medium');
      ?>
        <div class="thread-item">
          <a href="<?php echo get_permalink($item->ID); ?>">
            <?php if ($thumb): ?>
              <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($item->ID)); ?>">
            <?php endif; ?>
            <h3><?php echo esc_html(get_the_title($item->ID)); ?></h3>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>


<?php get_template_part('content/song-nav'); ?>
</div>
