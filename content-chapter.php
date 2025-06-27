<div <?php post_class(); ?>>
    <?php do_action( 'post_before' ); ?>
    <?php ct_author_featured_image(); ?>

<article>

<hr style="border: none; border-top: 1px solid #ccc; margin: 1.5em auto; max-width: 60%;">

    <!-- Page Links Key -->
  <div style="background-color:#f0f0f0; color:#111; padding:0.51em 0.68em; border-radius:6px; font-size:0.81em; line-height:1.4; margin:0 auto 1em; box-shadow:0 0 3px rgba(0,0,0,0.2); max-width:30%; text-align:left;">
    <div style="font-weight:bold; text-decoration:underline; font-size:0.92em; text-align:center; margin-bottom:0.4em;">Page Links Key:</div>
    🎹 <strong>Artist Profiles</strong><br>
    👤 <strong>Biographical Figures</strong><br>
    📚 <strong>Book Citations</strong><br>
    🔎 <strong>Lexicon Entries</strong><br>
    🎬 <strong>Movies Referenced</strong><br>
    💬 <strong>Quote Library</strong><br>
  </div>

  <hr style="border: none; border-top: 1px solid #ccc; margin: 1.5em auto; max-width: 60%;">

            <div class='post-header'>
            <h1 class='post-title'><?php the_title(); ?></h1>
        </div>

  <!-- Artist Info -->
  <?php
  $primary_artist = get_field('primary_artist');
  $song_title = get_field('primary_song_title');

  if ($primary_artist):
    $portrait = get_field('portrait_image', $primary_artist->ID);
    $img_url  = $portrait ? $portrait['sizes']['thumbnail'] : '';
    $artist_name = get_the_title($primary_artist->ID);
    $artist_link = get_permalink($primary_artist->ID);
    ?>
    <div class="artist-meta">
      <?php if ($img_url): ?>
        <a href="<?php echo esc_url($artist_link); ?>">
          <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($artist_name); ?>" class="artist-thumbnail rounded">
        </a>
      <?php endif; ?>

      <h2 class="artist-name">
        <a href="<?php echo esc_url($artist_link); ?>">
          🎹 <?php echo esc_html($artist_name); ?>
        </a>
      </h2>

      <?php if ($song_title): ?>
        <div class="song-title"><?php echo esc_html($song_title); ?></div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
        
		
		<div class="post-content">
    <?php ct_author_output_last_updated_date(); ?>
    <?php the_content(); ?>
    <?php wp_link_pages( array(
        'before' => '<p class="singular-pagination">' . esc_html__( 'Pages:', 'author' ),
        'after'  => '</p>',
    ) ); ?>
</div>

<?php
$chapter_id = get_the_ID();

$icons = [
  'artist'   => '🎹',
  'profile'  => '👤',
  'book'     => '📚',
  'concept'  => '🔎',
  'movie'    => '🎬',
  'quote'    => '💬',
];

$fields = [
  'primary_artist'       => 'artist',
  'secondary_artist'     => 'artist',
  'tertiary_artist'      => 'artist',
  'books_cited'          => 'book',
  'concepts_referenced'  => 'concept',
  'people_referenced'    => 'profile',
  'movies_referenced'    => 'movie',
  'quotes_referenced'    => 'quote',
];

$linked_items = [];

foreach ($fields as $field => $type) {
  $value = get_field($field, $chapter_id);

  if (empty($value)) continue;

  if (is_array($value)) {
    foreach ($value as $post) {
      if ($post instanceof WP_Post) {
        $linked_items[$post->ID] = $post;
      }
    }
  } elseif ($value instanceof WP_Post) {
    $linked_items[$value->ID] = $value;
  }
}
?>

<?php if (!empty($linked_items)) : ?>
  <div class="chapter-cpt-index">
    <h3 class="cpt-index-heading">Referenced Works & People</h3>
    <ul class="cpt-index-tags">
      <?php foreach ($linked_items as $item) :
        $type = get_post_type($item);
        $icon = $icons[$type] ?? '❓';
      ?>
        <li>
          <a href="<?php echo get_permalink($item); ?>">
            <span class="cpt-icon"><?php echo $icon; ?></span>
            <?php echo get_the_title($item); ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

    </article>
    <?php do_action( 'post_after' ); ?>
    <?php get_template_part( 'content/post-nav' ); ?>
    <?php comments_template(); ?>

</div>

