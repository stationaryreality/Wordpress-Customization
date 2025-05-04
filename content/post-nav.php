<?php get_header(); ?>

<div id="loop-container" class="loop-container">
  <div class="tag-posts-grid">

    <!-- Previous Post -->
    <?php
    $previous_post = get_adjacent_post( false, '', true ); // Get the previous post
    if ( $previous_post ) : ?>
      <div class="previous-post">
        <h2>Next Post</h2>
        <div class="post-thumbnail">
          <a href="<?php echo get_permalink( $previous_post->ID ); ?>">
            <?php echo get_the_post_thumbnail( $previous_post->ID, 'medium' ); ?>
          </a>
        </div>
        <h3><a href="<?php echo get_permalink( $previous_post->ID ); ?>"><?php echo get_the_title( $previous_post->ID ); ?></a></h3>
      </div>
    <?php endif; ?>

    <!-- Next Post -->
    <?php
    $next_post = get_adjacent_post( false, '', false ); // Get the next post
    if ( $next_post ) : ?>
      <div class="next-post">
        <h2>Previous Post</h2>
        <div class="post-thumbnail">
          <a href="<?php echo get_permalink( $next_post->ID ); ?>">
            <?php echo get_the_post_thumbnail( $next_post->ID, 'medium' ); ?>
          </a>
        </div>
        <h3><a href="<?php echo get_permalink( $next_post->ID ); ?>"><?php echo get_the_title( $next_post->ID ); ?></a></h3>
      </div>
    <?php endif; ?>
  
  </div>
</div>

<?php get_footer(); ?>
