<?php get_header(); ?>

<div id="loop-container" class="loop-container">
  <div class="tag-posts-grid">
    <!-- your tag post items go here -->
  </div>

  <!-- Move this OUTSIDE of the grid -->
  <div class="post-navigation-container">
    <?php
    // Get all chapters in the plugin-defined order
    $chapters = get_posts( array(
      'post_type'           => 'chapter',
      'posts_per_page'      => -1,
      'orderby'             => 'menu_order',
      'order'               => 'ASC',
      'suppress_filters'    => false, // lets Post Types Order work
      'fields'              => 'ids',
    ) );

    $current_id = get_the_ID();
    $current_index = array_search( $current_id, $chapters );

    // "Next Chapter" = the one AFTER current (forward in order)
    if ( $current_index !== false && isset( $chapters[ $current_index + 1 ] ) ) {
      $next_id = $chapters[ $current_index + 1 ]; ?>
      <div class="previous-post">
        <h2>Next Chapter</h2>
        <div class="post-thumbnail">
          <a href="<?php echo get_permalink( $next_id ); ?>">
            <?php echo get_the_post_thumbnail( $next_id, 'medium' ); ?>
          </a>
        </div>
        <h3><a href="<?php echo get_permalink( $next_id ); ?>"><?php echo get_the_title( $next_id ); ?></a></h3>
      </div>
    <?php }

    // "Previous Chapter" = the one BEFORE current (backward in order)
    if ( $current_index !== false && isset( $chapters[ $current_index - 1 ] ) ) {
      $prev_id = $chapters[ $current_index - 1 ]; ?>
      <div class="next-post">
        <h2>Previous Chapter</h2>
        <div class="post-thumbnail">
          <a href="<?php echo get_permalink( $prev_id ); ?>">
            <?php echo get_the_post_thumbnail( $prev_id, 'medium' ); ?>
          </a>
        </div>
        <h3><a href="<?php echo get_permalink( $prev_id ); ?>"><?php echo get_the_title( $prev_id ); ?></a></h3>
      </div>
    <?php } ?>
  </div>
</div>

<?php get_footer(); ?>
