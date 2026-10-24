<?php
$artist_id     = get_the_ID();
$bio           = get_field('bio', $artist_id);
$portrait      = get_field('portrait_image', $artist_id);
$img_url       = $portrait ? $portrait['sizes']['medium'] : '';
$wiki_slug     = get_field('wikipedia_slug', $artist_id);
$group_members = get_field('group_members', $artist_id);
$member_of     = get_field('member_of', $artist_id);

// Query songs where this artist is the primary artist
$primary_songs = get_posts([
    'post_type'      => 'song',
    'posts_per_page' => -1,
    'meta_query'     => [
        ['key' => 'song_artist', 'value' => $artist_id, 'compare' => '=']
    ],
    'orderby' => 'title',
    'order'   => 'ASC'
]);

// Query songs where this artist appears in additional_artists
$additional_songs = get_posts([
    'post_type'      => 'song',
    'posts_per_page' => -1,
    'meta_query'     => [
        ['key' => 'additional_artists', 'value' => $artist_id, 'compare' => 'LIKE']
    ],
    'orderby' => 'title',
    'order'   => 'ASC'
]);

// Merge and deduplicate
$all_song_ids = [];
$songs = [];
foreach (array_merge($primary_songs, $additional_songs) as $s) {
    if (!in_array($s->ID, $all_song_ids)) {
        $all_song_ids[] = $s->ID;
        $songs[] = $s;
    }
}

// Sort alphabetically by title
usort($songs, function($a, $b) {
    return strcmp(get_the_title($a->ID), get_the_title($b->ID));
});
?>

<div class="cpt-artist-content">

  <?php get_template_part('template-parts/navigation/artist'); ?>

  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="cpt-artist-thumbnail">
  <?php endif; ?>

  <h1><?php the_title(); ?></h1>

  <?php // ── GROUP MEMBERS (this artist IS a group) ── ?>
  <?php if (!empty($group_members)): ?>
    <div class="cpt-artist-members">
      <h3 class="cpt-artist-members-title">Members</h3>
      <div class="cpt-artist-members-grid">
        <?php foreach ($group_members as $member):
          $member_portrait = get_field('portrait_image', $member->ID);
          $member_thumb    = $member_portrait ? $member_portrait['sizes']['thumbnail'] : '';
        ?>
          <a href="<?php echo esc_url(get_permalink($member->ID)); ?>" class="cpt-artist-member-link">
            <?php if ($member_thumb): ?>
              <img src="<?php echo esc_url($member_thumb); ?>" alt="<?php echo esc_attr(get_the_title($member->ID)); ?>" class="cpt-artist-member-thumb">
            <?php endif; ?>
            <span class="cpt-artist-member-name"><?php echo esc_html(get_the_title($member->ID)); ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <?php // ── MEMBER OF (this artist belongs to groups) ── ?>
  <?php if (!empty($member_of)): ?>
    <div class="cpt-artist-memberof">
      <h3 class="cpt-artist-memberof-title">Member of</h3>
      <div class="cpt-artist-memberof-grid">
        <?php foreach ($member_of as $group):
          $group_portrait = get_field('portrait_image', $group->ID);
          $group_thumb    = $group_portrait ? $group_portrait['sizes']['thumbnail'] : '';
        ?>
          <a href="<?php echo esc_url(get_permalink($group->ID)); ?>" class="cpt-artist-memberof-link">
            <?php if ($group_thumb): ?>
              <img src="<?php echo esc_url($group_thumb); ?>" alt="<?php echo esc_attr(get_the_title($group->ID)); ?>" class="cpt-artist-memberof-thumb">
            <?php endif; ?>
            <span class="cpt-artist-memberof-name"><?php echo esc_html(get_the_title($group->ID)); ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="cpt-artist-bio">
    <?php if ($bio): ?>
      <?php echo wp_kses_post($bio); ?>
    <?php elseif ($wiki_slug): ?>
      <p><?php echo kp_get_wikipedia_intro($wiki_slug); ?></p>
    <?php else: ?>
      <?php the_content(); ?>
    <?php endif; ?>
  </div>

  <?php
  // --- LYRICS: explicit lyric_artist, otherwise fall back to song primary ---
  $lyrics = [];

  if (!empty($songs)) {
      foreach ($songs as $song) {

          $song_lyrics = get_posts([
              'post_type'      => 'lyric',
              'posts_per_page' => -1,
              'orderby'        => 'title',
              'order'          => 'ASC',
              'meta_query'     => [
                  ['key' => 'song', 'value' => $song->ID, 'compare' => '=']
              ]
          ]);

          foreach ($song_lyrics as $lyric) {
              $lyric_artists = get_field('lyric_artist', $lyric->ID);

              // A: lyric_artist is explicitly set → only listed artists get it
              if (!empty($lyric_artists) && is_array($lyric_artists)) {
                  foreach ($lyric_artists as $l_artist) {
                      $lid = is_object($l_artist) ? $l_artist->ID : $l_artist;
                      if ($lid == $artist_id) {
                          $lyrics[$lyric->ID] = $lyric;
                          break;
                      }
                  }
              }
              // B: lyric_artist is blank → belongs to the song's primary artist
              else {
                  $song_primary = get_field('song_artist', $song->ID);
                  $pid = $song_primary ? (is_object($song_primary) ? $song_primary->ID : $song_primary) : 0;

                  if ($pid == $artist_id) {
                      $lyrics[$lyric->ID] = $lyric;
                  }
              }
          }
      }
  }

  $lyrics = array_values($lyrics);
  usort($lyrics, function($a, $b) {
      return strcmp(get_the_title($a->ID), get_the_title($b->ID));
  });

  if (!empty($lyrics)):
      get_template_part(
          'template-parts/render/content-objects',
          null,
          ['posts' => $lyrics, 'title' => 'Song Excerpts']
      );
  endif;
  ?>

  <?php if (!empty($songs)): ?>
    <div class="cpt-artist-songs-section">
      <h2>Songs</h2>
      <div class="cpt-artist-song-grid">
        <?php foreach ($songs as $song):
          $cover = get_field('cover_image', $song->ID);
          $song_img_url = $cover ? $cover['sizes']['thumbnail'] : '';
        ?>
          <div class="cpt-artist-song-card">
            <a href="<?php echo get_permalink($song->ID); ?>">
              <?php if ($song_img_url): ?>
                <img src="<?php echo esc_url($song_img_url); ?>" alt="<?php echo esc_attr(get_the_title($song->ID)); ?>">
              <?php endif; ?>
              <h3><?php echo esc_html(get_the_title($song->ID)); ?></h3>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <?php
  $featured_chapters  = [];
  $featured_fragments = [];
  $referenced_in      = [];

  if (!empty($songs)) {
      foreach ($songs as $song) {
          $roles = kp_get_song_thread_roles($song->ID);

          foreach (array_merge($roles['chapter']['primary'], $roles['chapter']['secondary']) as $item) {
              $featured_chapters[$item->ID] = $item;
          }
          foreach (array_merge($roles['fragment']['primary'], $roles['fragment']['secondary']) as $item) {
              $featured_fragments[$item->ID] = $item;
          }
          foreach (array_merge($roles['chapter']['supporting'], $roles['fragment']['supporting']) as $item) {
              $referenced_in[$item->ID] = $item;
          }
      }
  }

  foreach ($featured_chapters as $id => $item) { unset($referenced_in[$id]); }
  foreach ($featured_fragments as $id => $item) { unset($referenced_in[$id]); }

  get_template_part('template-parts/views/featured-in-grid', null, ['title' => 'Narrative Threads', 'items' => $featured_chapters]);
  get_template_part('template-parts/views/featured-in-grid', null, ['title' => 'Narrative Fragments', 'items' => $featured_fragments]);
  get_template_part('template-parts/views/featured-in-grid', null, ['title' => 'Referenced In', 'items' => $referenced_in]);
  ?>

</div>