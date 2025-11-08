<?php
/**
 * Template Part: Element Grid (Image-style)
 *
 * Expected args:
 * - query: WP_Query object (optional)
 * - title: Section title (optional)
 * - emoji: optional icon/emoji prefix
 * - search_term: optional string to show context of search
 */

$query       = $args['query'] ?? null;
$title       = $args['title'] ?? 'Elements';
$emoji       = $args['emoji'] ?? '';
$search_term = $args['search_term'] ?? '';

// Default to all elements if no query passed
if (!$query) {
  $query = new WP_Query([
    'post_type'      => 'element',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
  ]);
}

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
        // You can swap this for another ACF field if Elements use a specific image field
        $image = get_field('image_file') ?: get_post_thumbnail_id();
        $img_url = '';

        if (is_array($image)) {
          $img_url = $image['sizes']['medium'] ?? $image['url'];
        } elseif ($image) {
          $img_url = wp_get_attachment_image_url($image, 'medium');
        }

        $excerpt = get_the_excerpt();
      ?>
      <div class="cited-item">
        <a href="<?php the_permalink(); ?>">
          <?php if ($img_url): ?>
            <img src="<?php echo esc_url($img_url); ?>"
                 alt="<?php the_title_attribute(); ?>"
                 style="width:150px; height:150px; object-fit:cover;">
          <?php endif; ?>
          <h3><?php the_title(); ?></h3>
        </a>

        <?php if ($excerpt): ?>
          <p style="margin:0.5rem 0 0;font-size:0.9em;color:#555;">
            <?php echo esc_html(wp_trim_words($excerpt, 20)); ?>
          </p>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<?php wp_reset_postdata(); ?>
