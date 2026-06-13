<?php
$artist_id  = get_the_ID();
$bio        = get_field('bio', $artist_id);
$portrait   = get_field('portrait_image', $artist_id);
$img_url    = $portrait ? $portrait['sizes']['medium'] : '';
$wiki_slug  = get_field('wikipedia_slug', $artist_id);

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

<div class="person-content">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="artist-thumbnail">
  <?php endif; ?>

  <h1><?php the_title(); ?></h1>

  <div class="person-bio">
    <?php if ($bio): ?>
      <?php echo wp_kses_post($bio); ?>
    <?php elseif ($wiki_slug): ?>
      <p><?php echo get_wikipedia_intro($wiki_slug); ?></p>
    <?php else: ?>
      <?php the_content(); ?>
    <?php endif; ?>
  </div>

  <?php
  // === Songs by Artist ===
  $songs = get_posts([
    'post_type'      => 'song',
    'posts_per_page' => -1,
    'meta_query'     => [
      [
        'key'     => 'song_artist', // ACF field name
        'value'   => $artist_id,
        'compare' => '='
      ]
    ],
    'orderby'        => 'title',
    'order'          => 'ASC'
  ]);

  if ($songs): ?>
    <div class="artist-songs">
      <h2>Songs</h2>
      <div class="song-grid">
        <?php foreach ($songs as $song):
          $cover = get_field('cover_image', $song->ID);
          $img_url = $cover ? $cover['sizes']['thumbnail'] : '';
        ?>
          <div class="book-item">
            <a href="<?php echo get_permalink($song->ID); ?>">
              <?php if ($img_url): ?>
                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr(get_the_title($song->ID)); ?>">
              <?php endif; ?>
              <h3><?php echo esc_html(get_the_title($song->ID)); ?></h3>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>


  <?php
// === Lyrics (via Songs → Artist) ===
$lyrics = [];

foreach ($songs as $song) {

    $song_lyrics = get_posts([
        'post_type'      => 'lyric',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => [
            [
                'key'     => 'song',
                'value'   => $song->ID,
                'compare' => '='
            ]
        ]
    ]);

    foreach ($song_lyrics as $lyric) {
        $lyrics[$lyric->ID] = $lyric;
    }
}

$lyrics = array_values($lyrics);

if (!empty($lyrics)): ?>
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

$featured_chapters  = [];
$featured_fragments = [];
$referenced_in      = [];

foreach ($songs as $song) {

    $roles = kp_get_song_thread_roles($song->ID);

    foreach (
        array_merge(
            $roles['chapter']['primary'],
            $roles['chapter']['secondary']
        ) as $item
    ) {
        $featured_chapters[$item->ID] = $item;
    }

    foreach (
        array_merge(
            $roles['fragment']['primary'],
            $roles['fragment']['secondary']
        ) as $item
    ) {
        $featured_fragments[$item->ID] = $item;
    }

    foreach (
        array_merge(
            $roles['chapter']['supporting'],
            $roles['fragment']['supporting']
        ) as $item
    ) {
        $referenced_in[$item->ID] = $item;
    }
}

get_template_part(
    'template-parts/views/featured-in-grid',
    null,
    [
        'title' => 'Narrative Threads',
        'items' => $featured_chapters,
    ]
);

get_template_part(
    'template-parts/views/featured-in-grid',
    null,
    [
        'title' => 'Narrative Fragments',
        'items' => $featured_fragments,
    ]
);

get_template_part(
    'template-parts/views/featured-in-grid',
    null,
    [
        'title' => 'Referenced In',
        'items' => $referenced_in,
    ]
);
?>

  <?php get_template_part('content/artist-nav'); ?>
</div>
