<?php
/**
 * Template Part: Element Grid
 * Styled to visually align with Chapter / Fragment grids
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

// Default query
if (!$query) {
  $query = new WP_Query([
    'post_type'      => 'element',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
  ]);
}

if (!$query->have_posts()) return;
?>

<section class="cpt-section element-grid" style="margin-bottom:4rem;">

  <h2>
    <?php if ($emoji) echo esc_html($emoji) . ' '; ?>
    <?php echo esc_html($title); ?>

    <?php if ($search_term): ?>
      <span style="font-weight:normal;font-size:0.9em;color:#666;">
        containing “<?php echo esc_html($search_term); ?>”
      </span>
    <?php endif; ?>
  </h2>

  <div class="tag-posts-grid">

    <?php while ($query->have_posts()): $query->the_post(); ?>

      <?php
        $image = get_field('image_file') ?: get_post_thumbnail_id();
        $img_url = '';

        if (is_array($image)) {
          $img_url = $image['sizes']['large'] ?? $image['url'];
        } elseif ($image) {
          $img_url = wp_get_attachment_image_url($image, 'large');
        }
      ?>

      <div class="tag-post-item">

        <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
          <?php if ($img_url): ?>
            <img
              src="<?php echo esc_url($img_url); ?>"
              alt="<?php the_title_attribute(); ?>"
            >
          <?php endif; ?>
        </a>

        <a href="<?php the_permalink(); ?>" class="tag-post-title">
          <?php the_title(); ?>
        </a>

        <?php if (get_the_excerpt()): ?>
          <p class="tag-post-excerpt">
            <?php echo esc_html(wp_trim_words(get_the_excerpt(), 20)); ?>
          </p>
        <?php endif; ?>

      </div>

    <?php endwhile; ?>

  </div>

</section>

<?php wp_reset_postdata(); ?>