<?php get_header(); ?>

<?php
$search_term = get_search_query();
$cpts = [
  'artist'  => ['icon' => '🎹', 'label' => 'Artists'],
  'profile' => ['icon' => '👤', 'label' => 'Profiles'],
  'book'    => ['icon' => '📚', 'label' => 'Books'],
  'concept' => ['icon' => '🔎', 'label' => 'Lexicon'],
  'movie'   => ['icon' => '🎬', 'label' => 'Movies'],
  'quote'   => ['icon' => '💬', 'label' => 'Quotes'],
  'chapter' => ['icon' => '🧵', 'label' => 'Narrative Threads'],
];

// Count total results across all CPTs
$total_results = 0;
foreach ($cpts as $type => $_) {
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
foreach ($cpts as $type => $info) {
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

  if ($query->have_posts()) {
    echo "<section style='margin-bottom: 4rem'>";
    echo "<h2 style='margin-bottom: 1rem;'>{$info['icon']} {$info['label']} containing “" . esc_html($search_term) . "”</h2>";

    set_query_var('grid_cards_args', $query_args);
    get_template_part('template-parts/loop/grid-cards');

    echo "</section>";
  }

  wp_reset_postdata();
}
?>
</div>

<?php get_footer(); ?>
