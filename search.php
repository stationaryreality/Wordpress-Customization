<?php
get_header();

$search_term = get_search_query();

$cpt_sections = [
  'artist'        => ['title' => 'Artists Featured',          'emoji' => '🎤'],
  'rapper'        => ['title' => 'Artists Featured',          'emoji' => '🎧'],
  'profile'       => ['title' => 'People Referenced',         'emoji' => '👤'],
  'lyric'         => ['title' => 'Song Excerpts',             'emoji' => '🎼'],
  'quote'         => ['title' => 'Quote Library',             'emoji' => '💬'],
  'concept'       => ['title' => 'Lexicon',                   'emoji' => '🔎'],
  'book'          => ['title' => 'Books Cited',               'emoji' => '📚'],
  'movie'         => ['title' => 'Movies Referenced',         'emoji' => '🎬'],
  'chapter'       => ['title' => 'Narrative Threads',         'emoji' => '🧵'],
  'fragment'      => ['title' => 'Narrative Fragments',       'emoji' => '📜'],
  'reference'     => ['title' => 'External References',       'emoji' => '📰'],
  'theme'         => ['title' => 'Themes',                    'emoji' => '🎨'],
  'organization'  => ['title' => 'Organizations Referenced',  'emoji' => '🏢'],
  'image'         => ['title' => 'Images Referenced',         'emoji' => '🖼'],
  'song'          => ['title' => 'Songs Featured',            'emoji' => '🎵'],
  'excerpt'       => ['title' => 'Excerpts Referenced',       'emoji' => '📖'],
];

echo '<main class="search-results">';
echo '<h1>Search results for “' . esc_html($search_term) . '”</h1>';

foreach ($cpt_sections as $type => $info) {
  // Special case: artist + rapper merged
  if ($type === 'artist') {
    get_template_part(
      "template-parts/search/artist",
      null,
      [
        'info'        => $info,
        'search_term' => $search_term,
      ]
    );
    continue;
  }

  // Normal query for this CPT
  $query = new WP_Query([
    'post_type'      => $type,
    's'              => $search_term,
    'posts_per_page' => -1,
    'relevanssi'     => true,
  ]);

  if (function_exists('relevanssi_do_query')) {
    relevanssi_do_query($query);
  }

  // Try to load a specific template, else fall back to default
  $template_path = locate_template("template-parts/search/{$type}.php");
  if ($template_path) {
    get_template_part(
      "template-parts/search/{$type}",
      null,
      [
        'query'       => $query,
        'info'        => $info,
        'search_term' => $search_term,
      ]
    );
  } else {
    get_template_part(
      "template-parts/search/default",
      null,
      [
        'query'       => $query,
        'info'        => $info,
        'search_term' => $search_term,
      ]
    );
  }
}

echo '</main>';

get_footer();
