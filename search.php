<?php get_header(); ?>

<?php
$search_term = get_search_query();

// CPTs mapped to display title + emoji
$cpt_sections = [
  'artist'        => ['title' => 'Artists Featured',          'emoji' => '🎤'],
  'rapper'        => ['title' => 'Artists Featured',          'emoji' => '🎧'],
  'profile'       => ['title' => 'People Referenced',         'emoji' => '👤'],
  'lyric'         => ['title' => 'Song Excerpts',             'emoji' => '🎼'],
  'quote'         => ['title' => 'Quote Library',             'emoji' => '💬'],
  'concept'       => ['title' => 'Lexicon',                   'emoji' => '🔎'],
  'book'          => ['title' => 'Books Cited',               'emoji' => '📚'],
  'movie'         => ['title' => 'Movies Referenced',         'emoji' => '🎬'],
  'chapter'       => ['title' => 'Narrative Threads',         'emoji' => '📜'],
  'reference'     => ['title' => 'External References',       'emoji' => '📰'],
  'theme'         => ['title' => 'Themes',                    'emoji' => '🎨'],
  'organization'  => ['title' => 'Organizations Referenced',  'emoji' => '🏢'],
  'image'         => ['title' => 'Images Referenced',         'emoji' => '🖼'],
  'song'          => ['title' => 'Songs Featured',            'emoji' => '🎵'],
];

// First count total results across CPTs
$total_results = 0;
foreach ($cpt_sections as $type => $_) {
  $count_args = [
    'post_type'      => $type,
    's'              => $search_term,
    'posts_per_page' => 1,
    'fields'         => 'ids',
    'relevanssi'     => true,
  ];
  $query = new WP_Query($count_args);
  if (function_exists('relevanssi_do_query')) {
    relevanssi_do_query($query);
  }
  $total_results += $query->found_posts;
}
?>

<div class="archive-header">
  <div class="post-header">
    <h1 class="post-title">
      <?php
      if ($total_results) {
        printf( esc_html( _n( '%1$d result for "%2$s"', '%1$d results for "%2$s"', $total_results, 'author' ) ), $total_results, esc_html($search_term) );
      } else {
        printf( esc_html__( 'No results for "%s"', 'author' ), esc_html($search_term) );
      }
      ?>
    </h1>
    <?php get_search_form(); ?>
  </div>
</div>

<div class="hub-section">

<?php
$skip_types = []; // Array to track CPTs we already handled

foreach ($cpt_sections as $type => $info) {

  // Skip 'rapper' here if we handle it merged with 'artist' below
  if (in_array($type, $skip_types, true)) {
    continue;
  }

  // Special case: merge artist + rapper with rounded headshots
  if ($type === 'artist') {
    $artist_query = new WP_Query([
      'post_type'      => 'artist',
      's'              => $search_term,
      'posts_per_page' => -1,
      'relevanssi'     => true,
    ]);
    if (function_exists('relevanssi_do_query')) relevanssi_do_query($artist_query);

    $rapper_query = new WP_Query([
      'post_type'      => 'rapper',
      's'              => $search_term,
      'posts_per_page' => -1,
      'relevanssi'     => true,
    ]);
    if (function_exists('relevanssi_do_query')) relevanssi_do_query($rapper_query);

    if (!$artist_query->have_posts() && !$rapper_query->have_posts()) continue;

    echo "<section style='margin-bottom: 4rem'>";
    echo "<h2 style='margin-bottom: 1rem;'>{$info['emoji']} {$info['title']} containing “" . esc_html($search_term) . "”</h2>";
    echo '<div class="author-grid">';

    // Loop artists first, then rappers
    foreach ([$artist_query, $rapper_query] as $q) {
      while ($q->have_posts()): $q->the_post();
        $portrait = get_field('portrait_image');
        $img_url = $portrait ? $portrait['sizes']['thumbnail'] : '';
        ?>
        <div class="book-item" style="text-align:center;">
          <a href="<?php the_permalink(); ?>" style="display:inline-block;">
            <?php if ($img_url): ?>
              <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" style="border-radius: 50%; width: 100px; height: 100px; object-fit: cover;">
            <?php endif; ?>
            <h3 style="margin-top: 0.5rem;"><?php the_title(); ?></h3>
          </a>
        </div>
      <?php endwhile;
    }
    echo '</div></section>';
    wp_reset_postdata();

    // Mark 'rapper' as handled, so it won't show separately
    $skip_types[] = 'rapper';

    continue; // Skip rest of main loop for 'artist'
  }

  // --- Normal rendering for all other CPTs ---
  $query_args = [
    'post_type'      => $type,
    's'              => $search_term,
    'posts_per_page' => -1,
    'relevanssi'     => true,
  ];

  $query = new WP_Query($query_args);
  if (function_exists('relevanssi_do_query')) {
    relevanssi_do_query($query);
  }

  if (!$query->have_posts()) continue;

  echo "<section style='margin-bottom: 4rem'>";
  echo "<h2 style='margin-bottom: 1rem;'>{$info['emoji']} {$info['title']} containing “" . esc_html($search_term) . "”</h2>";

  switch ($type) {
    case 'artist':
    case 'rapper':
    case 'profile':
      // This block will not run for 'artist' or 'rapper' because of the skip
      // But keep it in case profile is used here
      echo '<div class="author-grid">';
      while ($query->have_posts()): $query->the_post();
        $portrait = get_field('portrait_image');
        $img_url = $portrait ? $portrait['sizes']['thumbnail'] : '';
        ?>
        <div class="book-item" style="text-align:center;">
          <a href="<?php the_permalink(); ?>" style="display:inline-block;">
            <?php if ($img_url): ?>
              <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" style="border-radius: 50%; width: 100px; height: 100px; object-fit: cover;">
            <?php endif; ?>
            <h3 style="margin-top: 0.5rem;"><?php the_title(); ?></h3>
          </a>
        </div>
      <?php endwhile;
      echo '</div>';
      break;

    case 'book':
    case 'movie':
      echo '<div class="cited-grid">';
      while ($query->have_posts()): $query->the_post();
        $cover = get_field('cover_image');
        $img_url = $cover ? $cover['sizes']['medium'] : '';
        $byline = $type === 'book' ? get_field('author') : get_field('director');
        $summary = get_field('summary');
        ?>
        <div class="cited-item">
          <a href="<?php the_permalink(); ?>">
            <?php if ($img_url): ?>
              <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>">
            <?php endif; ?>
            <h3><?php the_title(); ?></h3>
          </a>
          <?php if ($byline): ?>
            <p><strong><?php echo esc_html($byline); ?></strong></p>
          <?php endif; ?>
          <?php if ($summary): ?>
            <p><?php echo esc_html(wp_trim_words($summary, 25)); ?></p>
          <?php endif; ?>
        </div>
      <?php endwhile;
      echo '</div>';
      break;

    case 'concept':
    case 'lyric':
      echo '<div class="concept-list">';
      while ($query->have_posts()): $query->the_post();
        $field = $type === 'concept' ? 'definition' : 'lyric_plain_text';
        $content = get_field($field);
        ?>
        <div class="concept-entry" style="margin-bottom: 2rem; border-bottom: 1px solid #ddd; padding-bottom: 1rem;">
          <h2 style="margin-bottom: 0.5rem;">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
          </h2>
          <?php if ($content): ?>
            <p><?php echo esc_html(wp_trim_words($content, 30)); ?></p>
          <?php endif; ?>
        </div>
      <?php endwhile;
      echo '</div>';
      break;

    case 'quote':
      echo '<div class="quote-list">';
      while ($query->have_posts()): $query->the_post();
        $quote_html = get_field('quote_html_block');
        ?>
        <div class="quote-entry" style="margin-bottom: 2rem; border-bottom: 1px solid #ddd; padding-bottom: 1rem;">
          <h2 style="margin-bottom: 0.5rem;">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
          </h2>
          <?php if ($quote_html): ?>
            <p><?php echo esc_html(wp_trim_words(strip_tags($quote_html), 30)); ?></p>
          <?php endif; ?>
        </div>
      <?php endwhile;
      echo '</div>';
      break;

    case 'chapter':
      echo '<div class="tag-posts-grid">';
      while ($query->have_posts()): $query->the_post(); ?>
        <div class="tag-post-item">
          <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
            <?php if (has_post_thumbnail()): ?>
              <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
            <?php endif; ?>
          </a>
          <a href="<?php the_permalink(); ?>" class="tag-post-title"><?php the_title(); ?></a>
          <p class="tag-post-excerpt"><?php the_excerpt(); ?></p>
        </div>
      <?php endwhile;
      echo '</div>';
      break;
      default:
  echo '<div class="cited-grid">';
  while ($query->have_posts()): $query->the_post();
    $thumb_url = '';
    if (has_post_thumbnail()) {
      $thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
    } elseif (get_field('cover_image')) {
      $cover = get_field('cover_image');
      $thumb_url = $cover['sizes']['medium'] ?? '';
    } elseif (get_field('portrait_image')) {
      $portrait = get_field('portrait_image');
      $thumb_url = $portrait['sizes']['medium'] ?? '';
    } elseif (get_field('image_file')) {
      $image_file = get_field('image_file');
      $thumb_url = $image_file['sizes']['medium'] ?? '';
    }
    ?>
    <div class="cited-item">
      <a href="<?php the_permalink(); ?>">
        <?php if ($thumb_url): ?>
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title(); ?>">
        <?php endif; ?>
        <h3><?php the_title(); ?></h3>
      </a>
    </div>
  <?php endwhile;
  echo '</div>';
  break;

  }

  wp_reset_postdata();
  echo "</section>";
}
?>

</div>

<?php get_footer(); ?>
