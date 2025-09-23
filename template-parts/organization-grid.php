<?php
$query       = $args['query'];
$title       = $args['title'] ?? 'Organizations';
$emoji       = $args['emoji'] ?? '';
$search_term = $args['search_term'] ?? '';

if (!$query->have_posts()) return;
?>

<section class="cited-grid-wrapper cited-grid-wrapper--organizations">
  <h1 class="cited-grid__heading">
    <?php if ($emoji) echo $emoji . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term): ?>
      containing “<?php echo esc_html($search_term); ?>”
    <?php endif; ?>
  </h1>

  <div class="cited-grid cited-grid--organizations">
    <?php while ($query->have_posts()): $query->the_post(); ?>
      <?php
        $bio    = get_field('org_bio');
        $cover  = get_field('cover_image');
        $img_url = $cover ? $cover['sizes']['thumbnail'] : '';
      ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class('cited-item'); ?>>
        <a class="cited-item__link" href="<?php the_permalink(); ?>">
          <?php if ($img_url): ?>
            <div class="cited-item__thumb" aria-hidden="true">
              <img src="<?php echo esc_url($img_url); ?>"
                   alt="<?php echo esc_attr(get_the_title()); ?>"
                   loading="lazy"
                   decoding="async"
                   style="aspect-ratio:1/1; object-fit:cover;" />
            </div>
          <?php endif; ?>

          <h3 class="cited-item__title"><?php the_title(); ?></h3>
        </a>

        <?php if ($bio): ?>
          <p class="cited-item__meta"><?php echo esc_html(wp_trim_words($bio, 20)); ?></p>
        <?php endif; ?>
      </article>
    <?php endwhile; ?>
  </div>
</section>

<?php wp_reset_postdata(); ?>
