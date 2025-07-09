<?php
$lyric_html = get_field('lyric_cover_block_full');
$lyric_text = get_field('lyric_plain_text');
$portrait = get_field('portrait_image', get_the_ID());
$img_url = $portrait ? $portrait['sizes']['thumbnail'] : '';
?>

<div class="person-content">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="author-thumbnail">
  <?php endif; ?>

  <h1><?php the_title(); ?></h1>

  <div class="person-bio">
    <?php if ($lyric_html): ?>
      <div class="lyric-block lyric-cover-html">
        <?php echo do_blocks($lyric_html); ?>
      </div>
    <?php endif; ?>

    <?php if ($lyric_text): ?>
      <div class="lyric-block lyric-raw-text">
        <?php echo nl2br(esc_html($lyric_text)); ?>
      </div>
    <?php endif; ?>
  </div>

  <?php get_template_part('content/lyric-nav'); ?>
</div>
