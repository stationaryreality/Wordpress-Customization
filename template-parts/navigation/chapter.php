<?php
// template-parts/navigation/chapter.php
// True template part for Chapter Next/Previous navigation

$current_id = get_the_ID();

// Get all chapters in the plugin-defined order
$chapters = get_posts( array(
  'post_type'        => 'chapter',
  'posts_per_page'   => -1,
  'orderby'          => 'menu_order',
  'order'            => 'ASC',
  'suppress_filters' => false, // lets Post Types Order plugin work
  'fields'           => 'ids',
) );

$current_index = array_search( $current_id, $chapters );
?>

<div class="cpt-chapter-nav">
  <?php 
  // "Next Chapter" = the one AFTER current (forward in order)
  if ( $current_index !== false && isset( $chapters[ $current_index + 1 ] ) ) {
    $next_id = $chapters[ $current_index + 1 ]; 
    $next_thumb = get_the_post_thumbnail_url( $next_id, 'medium' );
    ?>
    <div class="cpt-chapter-nav-next core-card">
      <h2>Next Chapter</h2>
      <a href="<?php echo get_permalink( $next_id ); ?>">
        <?php if ( $next_thumb ) : ?>
          <img src="<?php echo esc_url( $next_thumb ); ?>" alt="<?php echo esc_attr( get_the_title( $next_id ) ); ?>" class="core-card-image">
        <?php endif; ?>
        <h3 class="core-card-title"><?php echo get_the_title( $next_id ); ?></h3>
      </a>
    </div>
  <?php } ?>

  <?php 
  // "Previous Chapter" = the one BEFORE current (backward in order)
  if ( $current_index !== false && isset( $chapters[ $current_index - 1 ] ) ) {
    $prev_id = $chapters[ $current_index - 1 ]; 
    $prev_thumb = get_the_post_thumbnail_url( $prev_id, 'medium' );
    ?>
    <div class="cpt-chapter-nav-prev core-card">
      <h2>Previous Chapter</h2>
      <a href="<?php echo get_permalink( $prev_id ); ?>">
        <?php if ( $prev_thumb ) : ?>
          <img src="<?php echo esc_url( $prev_thumb ); ?>" alt="<?php echo esc_attr( get_the_title( $prev_id ) ); ?>" class="core-card-image">
        <?php endif; ?>
        <h3 class="core-card-title"><?php echo get_the_title( $prev_id ); ?></h3>
      </a>
    </div>
  <?php } ?>
</div>