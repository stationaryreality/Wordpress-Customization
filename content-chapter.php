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

    </article>
    <?php do_action( 'post_after' ); ?>
    <?php get_template_part( 'content/post-nav' ); ?>
    <?php comments_template(); ?>

</div>
