<?php
/* Template Name: Artists Featured */
get_header();
?>

<main id="primary" class="site-main artist-archive">

<?php
$artist_tiers = [
  'narrative'  => '📖 Narrative Artists',
  'featured'   => '🎧 Featured Artists',
  'referenced' => '🎤 Referenced Artists',
];

// ----- Non-Rappers Only -----
foreach ($artist_tiers as $feature_slug => $feature_label):
?>
  <div class="feature-group">
    <h3 class="feature-level"><?php echo esc_html($feature_label); ?></h3>

    <?php
    $non_rappers = new WP_Query([
      'post_type'      => 'artist',
      'posts_per_page' => -1,
      'tax_query'      => [
        [
          'taxonomy' => 'feature_level',
          'field'    => 'slug',
          'terms'    => $feature_slug,
        ],
        [
          'taxonomy' => 'artist_type',
          'field'    => 'slug',
          'terms'    => ['rapper'],
          'operator' => 'NOT IN',
        ],
      ],
      'orderby' => 'title',
      'order'   => 'ASC',
    ]);

    if ($non_rappers->have_posts()) :
      set_query_var('artist_query', $non_rappers);
      get_template_part('template-parts/artist-grid');
    endif;

    wp_reset_postdata();
    ?>
  </div>
<?php endforeach; ?>

</main>

<?php get_footer(); ?>