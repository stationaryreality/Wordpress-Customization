<?php
$query       = $args['query'];
$title       = $args['title'] ?? 'Shows';
$emoji       = $args['emoji'] ?? '';
$search_term = $args['search_term'] ?? '';

if (!$query->have_posts()) return;
?>

<section class="cited-grid-wrapper cited-grid-wrapper--shows">
  <h1 class="cited-grid__heading">
    <?php if ($emoji) echo $emoji . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term): ?>
      containing “<?php echo esc_html($search_term); ?>”
    <?php endif; ?>
  </h1>

  <div class="cited-grid cited-grid--shows">
    <?php while ($query->have_posts()): $query->the_post(); ?>
      <?php
        $creator = get_field('creator');
        $summary = get_field('summary');
        $cover   = get_field('cover_image');
        $img_url = $cover ? $cover['sizes']['medium'] : '';
      ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class('cited-item'); ?>>
        <a class="cited-item__link" href="<?php the_permalink(); ?>">
          <?php if ($img_url): ?>
            <div class="cited-item__thumb" aria-hidden="true">
              <img src="<?php echo esc_url($img_url); ?>"
                   alt="<?php echo esc_attr(get_the_title()); ?>"
                   loading="lazy"
                   decoding="async" />
            </div>
          <?php endif; ?>

          <h3 class="cited-item__title"><?php the_title(); ?></h3>
        </a>

        <?php if ($creator): ?>
          <p class="cited-item__meta"><strong><?php echo esc_html($creator); ?></strong></p>
        <?php endif; ?>

        <?php if ($summary): ?>
          <p class="cited-item__excerpt"><?php echo esc_html(wp_trim_words($summary, 25)); ?></p>
        <?php endif; ?>
      </article>
    <?php endwhile; ?>
  </div>
</section>

<?php wp_reset_postdata(); ?>
