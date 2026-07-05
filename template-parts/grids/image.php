<?php
$query       = $args['query'];
$title       = $args['title'] ?? 'Images';
$emoji       = $args['emoji'] ?? '';
$search_term = $args['search_term'] ?? '';
if (!$query->have_posts()) return;
?>

<section style="margin-bottom:4rem;">
  <h2>
    <?php if ($emoji) echo $emoji . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term): ?>
      <span style="font-weight:normal;font-size:0.9em;color:#666;">
        containing “<?php echo esc_html($search_term); ?>”
      </span>
    <?php endif; ?>
  </h2>

  <div class="cited-grid">
    <?php while ($query->have_posts()): $query->the_post(); ?>
      <?php
        $caption = get_field('image_caption');
        $image   = get_field('image_file');
        // Use ACF size for uniform dimensions; fallback to featured
        $img_url = $image ? $image['sizes']['medium'] : get_the_post_thumbnail_url(get_the_ID(), 'medium');
      ?>
      <div class="cited-item">
        <a href="<?php the_permalink(); ?>">
          <?php if ($img_url): ?>
            <img src="<?php echo esc_url($img_url); ?>"
                 alt="<?php the_title(); ?>"
                 style="width:150px; height:150px; object-fit:cover;">
          <?php endif; ?>
          <h3><?php the_title(); ?></h3>
        </a>
        <?php if ($caption): ?>
          <p style="margin:0.5rem 0 0;font-size:0.9em;color:#555;">
            <?php echo esc_html(wp_trim_words($caption, 20)); ?>
          </p>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<?php wp_reset_postdata(); ?>
