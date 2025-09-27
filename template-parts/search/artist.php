<?php
/**
 * Artist Search Template
 *
 * Expected args:
 * - $info        : metadata about the CPT (emoji, title, etc.)
 * - $search_term : the search term (or theme name if taxonomy view)
 * - $query       : WP_Query object (already run with Relevanssi or tax_query)
 */

$info        = $args['info'] ?? null;
$search_term = $args['search_term'] ?? '';
$query       = $args['query'] ?? null;

// Defensive: bail if query missing or invalid
if ( ! $query || ! ( $query instanceof WP_Query ) || ! $query->have_posts() ) {
    return;
}
?>

<section style="margin-bottom:4rem;">
  <h2>
    <?php echo esc_html( $info['emoji'] . ' ' . $info['title'] ); ?>
    containing “<?php echo esc_html( $search_term ); ?>”
  </h2>

  <?php 
    // artist-grid expects $artist_query
    set_query_var( 'artist_query', $query );
    get_template_part( 'template-parts/artist-grid' );
  ?>
</section>
