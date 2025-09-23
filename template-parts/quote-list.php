<?php
/**
 * Shared Quote List Template
 *
 * Expects:
 * - query       => WP_Query or get_posts() array
 * - title       => Section/page title
 * - emoji       => Emoji (optional)
 * - search_term => Optional (only used on search)
 */
$query       = $args['query'];
$title       = $args['title'] ?? 'Quotes';
$emoji       = $args['emoji'] ?? '';
$search_term = $args['search_term'] ?? '';

if (empty($query)) return;

// Normalize to iterable (WP_Query or array of posts)
$posts = $query instanceof WP_Query ? $query->posts : $query;
if (empty($posts)) return;
?>

<section class="quote-grid container" style="max-width: 800px; margin: auto; padding: 2rem 1rem;">
  <h1>
    <?php if ($emoji) echo $emoji . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term) : ?>
      containing “<?php echo esc_html($search_term); ?>”
    <?php endif; ?>
  </h1>
  <p class="intro-text">Collected quotes from books, chapters, and profiles across the site.</p>

  <div class="quote-list">
    <?php foreach ($posts as $quote): ?>
      <?php 
        $excerpt = get_field('quote_plain_text', $quote->ID);
        $source  = get_field('quote_source', $quote->ID); 
        $source_link  = $source ? get_permalink($source->ID) : '';
        $source_title = $source ? get_the_title($source->ID) : '';

        // Handle source image
        $image = '';
        if ($source) {
          $cover = get_field('cover_image', $source->ID);
          if ($cover) {
            $image = $cover['sizes']['thumbnail'];
          } elseif (has_post_thumbnail($source->ID)) {
            $image = get_the_post_thumbnail_url($source->ID, 'thumbnail');
          }
        }
      ?>
      <div class="quote-entry" style="display:flex; align-items:flex-start; gap:1rem; margin-bottom:2rem; border-bottom:1px solid #ddd; padding-bottom:1rem;">
        <?php if ($image): ?>
          <a href="<?php echo esc_url($source_link); ?>" class="quote-thumb">
            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($source_title); ?>" style="width:48px; height:48px; border-radius:50%; object-fit:cover;">
          </a>
        <?php endif; ?>

        <div class="quote-text">
          <h2 style="margin-bottom:0.5rem;">
            <a href="<?php echo get_permalink($quote); ?>">
              <?php echo esc_html(get_the_title($quote)); ?>
            </a>
          </h2>

          <?php if ($excerpt): ?>
            <p style="margin:0;"><?php echo esc_html(wp_trim_words($excerpt, 30, '...')); ?></p>
          <?php endif; ?>

          <?php if ($source): ?>
            <p style="margin-top:0.5rem; font-size:0.9rem; color:#666;">
              Source: <a href="<?php echo esc_url($source_link); ?>"><?php echo esc_html($source_title); ?></a>
            </p>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php if ($query instanceof WP_Query) wp_reset_postdata(); ?>
