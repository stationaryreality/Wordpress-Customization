<?php
/* Template Name: All CPT Index (Alphabetical) */
get_header();

// Emoji mapping
$icons = [
  'artist'        => '🎤',
  'song'          => '🎵',
  'lyric'         => '🎼',
  'profile'       => '👤',
  'quote'         => '💬',
  'concept'       => '🔎',
  'book'          => '📚',
  'movie'         => '🎬',
  'reference'     => '📰',
  'chapter'       => '🧵',
  'organizations' => '🏢',
  'image'         => '🖼',
  'theme'         => '🎨',
  'excerpt'       => '📖',
  'fragment'      => '📜',

];

// Relevant CPTs
$post_types = [
  'artist',
  'profile',
  'book',
  'concept',
  'movie',
  'quote',
  'lyric',
  'reference',
  'organizations',
  'image',
  'song',
  'chapter',
  'excerpt',
  'fragment'
];

// Query all CPT entries
$post_args = [
  'post_type'      => $post_types,
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
  'post_status'    => 'publish'
];
$post_query = new WP_Query($post_args);

// Query all themes (taxonomy terms)
$themes = get_terms([
  'taxonomy'   => 'theme',
  'hide_empty' => false,
  'orderby'    => 'name',
  'order'      => 'ASC'
]);

// Collect all entries
$entries = [];

// Add post-type entries
if ($post_query->have_posts()) {
  while ($post_query->have_posts()) {
    $post_query->the_post();
    $type  = get_post_type();
    $title = get_the_title();
    $url   = get_permalink();
    $icon  = $icons[$type] ?? '❓';

    $entries[] = [
      'title' => $title,
      'url'   => $url,
      'icon'  => $icon
    ];
  }
  wp_reset_postdata();
}

// Add theme taxonomy terms
foreach ($themes as $term) {
  $entries[] = [
    'title' => $term->name,
    'url'   => get_term_link($term),
    'icon'  => $icons['theme']
  ];
}

// Alphabetize the whole list
usort($entries, function ($a, $b) {
  return strcasecmp($a['title'], $b['title']);
});
?>

<main class="cpt-index-alphabetical">
  <header class="archive-header">
    <h1 class="post-title">All Entries (Alphabetical Index)</h1>
    <?php
$total_count = count($entries);
?>
<p class="post-meta">
  Total entries: <strong><?php echo $total_count; ?></strong>
</p>

<div class="pattern-wrapper">
    <?php echo apply_filters('the_content', '<!-- wp:block {"ref":22739} /-->'); ?>
</div>

  </header>

  <?php if (!empty($entries)) : ?>
    <ul class="cpt-alpha-list">
      <?php foreach ($entries as $entry) : ?>
        <li>
          <span class="cpt-icon"><?php echo $entry['icon']; ?></span>
          <a href="<?php echo esc_url($entry['url']); ?>"><?php echo esc_html($entry['title']); ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else : ?>
    <p>No entries found.</p>
  <?php endif; ?>
</main>

<?php get_footer(); ?>
