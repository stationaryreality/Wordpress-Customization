<?php
$query       = $args['query'];
$title       = $args['title'] ?? '';
$emoji       = $args['emoji'] ?? '';
$search_term = $args['search_term'] ?? '';
if (!$query->have_posts()) return;
?>

<section class="search-section search-section--images" style="margin-bottom:2rem;">
  <h2 style="margin-bottom:1rem;">
    <?php echo $emoji . ' ' . esc_html($title); ?>
    <?php if ($search_term): ?>
      <span style="font-weight:normal;font-size:0.9em;color:#666;">
        containing “<?php echo esc_html($search_term); ?>”
      </span>
    <?php endif; ?>
  </h2>

  <div class="cited-grid cited-grid--images">
    <?php while ($query->have_posts()): $query->the_post(); ?>
      <?php
        $caption = get_field('image_caption');
        $image   = get_field('image_file');
        $img_url = $image ? $image['url'] : '';
      ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class('cited-item'); ?>>
        <a class="cited-item__link" href="<?php the_permalink(); ?>">
          <?php if ($img_url): ?>
            <div class="cited-item__thumb" aria-hidden="true">
              <img src="<?php echo esc_url($img_url); ?>"
                   alt="<?php echo esc_attr(get_the_title()); ?>"
                   loading="lazy" decoding="async" />
            </div>
          <?php endif; ?>
          <h3 class="cited-item__title"><?php the_title(); ?></h3>
        </a>
        <?php if ($caption): ?>
          <p class="cited-item__meta"><?php echo esc_html(wp_trim_words($caption, 20)); ?></p>
        <?php endif; ?>
      </article>
    <?php endwhile; ?>
  </div>
</section>

<?php wp_reset_postdata(); ?>
