<?php
/**
 * Shared Reference List Template
 *
 * Expects:
 * - query       => WP_Query object
 * - title       => Section/page title
 * - emoji       => Emoji (optional, from centralized lookup)
 * - search_term => Optional (only used on search)
 */
$query       = $args['query'];
$title       = $args['title'] ?? 'References';
$emoji       = $args['emoji'] ?? '';
$search_term = $args['search_term'] ?? '';

if (!$query->have_posts()) return;
?>

<section class="reference-grid container" style="max-width:800px;margin:auto;padding:2rem 1rem;">
  <h1>
    <?php if ($emoji) echo $emoji . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term) : ?>
      containing “<?php echo esc_html($search_term); ?>”
    <?php endif; ?>
  </h1>
  <p class="intro-text">External sources, credits, and citations referenced throughout the site.</p>

  <div class="reference-list">
    <?php while ($query->have_posts()) : $query->the_post(); ?>
      <?php
        $source      = get_field('source_name', get_the_ID());
        $description = get_field('description', get_the_ID());
        $url         = get_field('url', get_the_ID());
        $credit      = get_field('credit_name', get_the_ID());
        $archive     = get_field('archive_link', get_the_ID());

        // ✅ Image handling: prefer ACF cover_image, fallback to featured image
        $cover = get_field('cover_image', get_the_ID());
        if ($cover) {
          $image_url = $cover['sizes']['thumbnail'];
        } elseif (has_post_thumbnail(get_the_ID())) {
          $image_url = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
        } else {
          $image_url = '';
        }
      ?>
      <div class="reference-entry" style="display:flex;align-items:flex-start;gap:1rem;margin-bottom:2rem;border-bottom:1px solid #ddd;padding-bottom:1rem;">
        <?php if ($image_url): ?>
          <a href="<?php the_permalink(); ?>" class="reference-thumb">
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title(); ?>" style="width:48px;height:48px;border-radius:50%;object-fit:cover;">
          </a>
        <?php endif; ?>

        <div class="reference-text">
          <h2 style="margin-bottom:0.5rem;">
            <a href="<?php the_permalink(); ?>">
              <?php the_title(); ?>
            </a>
          </h2>

          <?php if ($source): ?>
            <p style="margin:0.25rem 0;font-style:italic;color:#666;">
              <?php echo esc_html($source); ?>
            </p>
          <?php endif; ?>

          <?php if ($credit): ?>
            <p style="margin:0.25rem 0;color:#333;">
              <strong>Credit:</strong> <?php echo esc_html($credit); ?>
            </p>
          <?php endif; ?>

          <?php if ($description): ?>
            <p style="margin:0.5rem 0;"><?php echo esc_html(wp_trim_words($description, 30)); ?></p>
          <?php endif; ?>

          <?php if ($url || $archive): ?>
            <p style="margin-top:0.5rem;">
              <?php if ($url): ?>
                <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">View Source</a>
              <?php endif; ?>
              <?php if ($url && $archive): ?> | <?php endif; ?>
              <?php if ($archive): ?>
                <a href="<?php echo esc_url($archive); ?>" target="_blank" rel="noopener noreferrer">View Archive</a>
              <?php endif; ?>
            </p>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</section>
<?php wp_reset_postdata(); ?>
