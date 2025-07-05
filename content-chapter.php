<div <?php post_class(); ?>>
    <?php do_action( 'post_before' ); ?>
    <?php ct_author_featured_image(); ?>

<article>

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

$group_order = [
  'artist'  => ['title' => 'Artists Featured',    'emoji' => '🎹'],
  'profile' => ['title' => 'People Referenced',   'emoji' => '👤'],
  'lyric'   => ['title' => 'Song Excerpts',       'emoji' => '📻'],
  'quote'   => ['title' => 'Quote Library',       'emoji' => '💬'],
  'concept' => ['title' => 'Lexicon',             'emoji' => '🔎'],
  'book'    => ['title' => 'Books Cited',         'emoji' => '📚'],
  'movie'   => ['title' => 'Movies Referenced',   'emoji' => '🎬'],
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
  'lyrics_referenced'    => 'lyric', // New lyric CPT
];

$grouped_items = [];

foreach ($fields as $field => $type) {
  $value = get_field($field, $chapter_id);
  if (empty($value)) continue;

  $items = is_array($value) ? $value : [$value];
  foreach ($items as $item) {
    if ($item instanceof WP_Post) {
      $grouped_items[$type][$item->ID] = $item;
    }
  }
}
?>

<?php if (!empty($grouped_items)) : ?>
  <div class="chapter-cpt-index">
    <h3 class="cpt-index-heading">Referenced Works & People</h3>
    <?php foreach ($group_order as $type => $meta) :
      if (empty($grouped_items[$type])) continue;
    ?>
      <div class="cpt-group cpt-<?php echo esc_attr($type); ?>">
        <h4 class="cpt-group-heading">
          <span class="cpt-icon"><?php echo $meta['emoji']; ?></span>
          <?php echo esc_html($meta['title']); ?>
        </h4>
        <ul class="cpt-index-tags">
          <?php foreach ($grouped_items[$type] as $item) :
            $title = get_the_title($item);
            $permalink = get_permalink($item);
            $extra = '';

            // Extra content for quotes and lyrics
            if (in_array($type, ['quote', 'lyric'])) {
              $field = get_field('quote_text', $item->ID) ?: get_field('lyric_text', $item->ID);
              if ($field) {
                $extra = '<div class="cpt-snippet">– ' . esc_html(wp_trim_words($field, 25)) . '</div>';
              }
            }

            // Image for books/movies
            if (in_array($type, ['book', 'movie'])) {
              $image = get_field('cover_image', $item->ID);
              if ($image && isset($image['url'])) {
                $extra = '<div class="cpt-cover"><a href="' . esc_url($permalink) . '"><img src="' . esc_url($image['url']) . '" alt="' . esc_attr($title) . '" style="width:120px;height:auto;margin-top:0.5em;" /></a></div>';
              }
            }
          ?>
            <li>
              <a href="<?php echo esc_url($permalink); ?>">
                <?php echo esc_html($title); ?>
              </a>
              <?php echo $extra; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>


    </article>
    <?php do_action( 'post_after' ); ?>
    <?php get_template_part( 'content/post-nav' ); ?>
    <?php comments_template(); ?>

</div>
