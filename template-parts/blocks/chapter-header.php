<?php
$artist_name = get_field('artist_name');
$song_title = get_field('song_title');
$headshot = get_field('artist_headshot'); // This is an image array

if ($artist_name || $song_title || $headshot):
?>
<div class="chapter-header-block" style="text-align: center;">
    <?php if ($headshot): ?>
        <a href="<?php echo esc_url(get_tag_link($artist_name)); ?>">
            <img src="<?php echo esc_url($headshot['sizes']['medium']); ?>" alt="<?php echo esc_attr($artist_name); ?>" style="border-radius: 100%; max-width: 150px;" />
        </a>
    <?php endif; ?>

    <?php if ($artist_name): ?>
        <div class="artist-name">
            <a href="<?php echo esc_url(get_tag_link($artist_name)); ?>"><strong><?php echo esc_html($artist_name); ?></strong></a>
        </div>
    <?php endif; ?>

    <?php if ($song_title): ?>
        <div class="song-title"><?php echo esc_html($song_title); ?></div>
    <?php endif; ?>
</div>




<div class="chapter-block">
  <!-- existing header block content -->
</div>




<?php endif; ?>
