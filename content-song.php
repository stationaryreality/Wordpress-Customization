<?php
$song_id   = get_the_ID();
$cover     = get_field('cover_image', $song_id);
$img_url   = $cover ? $cover['sizes']['medium'] : '';
$artist_profile = get_field('song_artist');
?>

<div class="person-content">   <!-- 👈 THIS IS THE KEY WRAPPER -->

  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="album-cover" style="border-radius:0; aspect-ratio:1/1; object-fit:cover; max-width:300px; margin-bottom:1em;">
  <?php endif; ?>

  <h1><?php the_title(); ?></h1>

  <div class="person-bio">     <!-- 👈 same class as artist bio -->
    <?php the_content(); ?>
  </div>

  <?php if ($artist_profile): ?>
    <?php
      $portrait = get_field('portrait_image', $artist_profile->ID);
      $thumb    = $portrait ? $portrait['sizes']['thumbnail'] : '';
    ?>
    <div class="person-content" style="margin-top: 2em;">   <!-- keep this for the artist card -->
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
    'orderby'        => 'title',
    'order'          => 'ASC',
    'meta_query'     => [
      [
        'key'     => 'song',
        'value'   => $song_id,
        'compare' => '='
      ]
    ]
  ]);

  if (!empty($lyrics)) {
    get_template_part(
      'template-parts/render/content-objects',
      null,
      ['posts' => $lyrics, 'title' => 'Song Excerpts']
    );
  }
  ?>

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
  ?>

  <?php
  // === Narrative Threads (reuse same logic as artist) ===
  $roles = kp_get_song_thread_roles($song_id);

  $featured_chapters = array_merge(
    $roles['chapter']['primary'],
    $roles['chapter']['secondary']
  );

  $featured_fragments = array_merge(
    $roles['fragment']['primary'],
    $roles['fragment']['secondary']
  );

  $referenced_in = array_merge(
    $roles['chapter']['supporting'],
    $roles['fragment']['supporting']
  );

  get_template_part(
    'template-parts/views/featured-in-grid',
    null,
    ['title' => 'Narrative Threads', 'items' => $featured_chapters]
  );

  get_template_part(
    'template-parts/views/featured-in-grid',
    null,
    ['title' => 'Narrative Fragments', 'items' => $featured_fragments]
  );

  if (!empty($referenced_in)): ?>
    <div class="narrative-supporting" style="margin-top: 4em; text-align:center;">
      <h2>Referenced In</h2>
      <div class="thread-grid small-grid">
        <?php foreach ($referenced_in as $item):
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

</div>   <!-- 👈 close the wrapper -->