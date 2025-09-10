<?php
?>

<div class="person-content">
  <h1><?php the_title(); ?></h1>

  <div class="lyric-content">
    <?php the_content(); ?>
  </div>

  <?php get_template_part('content/lyric-nav'); ?>
</div>
