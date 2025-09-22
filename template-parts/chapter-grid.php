<?php
/**
 * Chapter Grid Template
 * Reusable grid for displaying Chapter CPTs
 *
 * Expected args:
 * - query: WP_Query object (optional, defaults to all chapters)
 * - title: Section title (optional)
 * - emoji: Emoji for section title (optional)
 * - search_term: Current search term (optional, for search context)
 */

$query        = $args['query'] ?? null;
$section_title = $args['title'] ?? '';
$emoji        = $args['emoji'] ?? '';
$search_term  = $args['search_term'] ?? '';

// If no query is passed, default to all chapters (homepage-style)
if (!$query) {
  $query = new WP_Query([
    'post_type'      => 'chapter',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
  ]);
}

if (!$query->have_posts()) return;
?>

<section class="cpt-section chapter-grid" style="margin-bottom:4rem;">
  <?php if ($section_title): ?>
    <h2>
      <?php echo esc_html(trim($emoji . ' ' . $section_title)); ?>
      <?php if ($search_term): ?>
        containing “<?php echo esc_html($search_term); ?>”
      <?php endif; ?>
    </h2>
  <?php endif; ?>

  <div class="tag-posts-grid">
    <?php while ($query->have_posts()): $query->the_post(); ?>
      <div class="tag-post-item">
        <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
          <?php if (has_post_thumbnail()): ?>
            <img src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium')); ?>" alt="<?php the_title_attribute(); ?>">
          <?php endif; ?>
        </a>
        <a href="<?php the_permalink(); ?>" class="tag-post-title"><?php the_title(); ?></a>
        <p class="tag-post-excerpt"><?php the_excerpt(); ?></p>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<?php wp_reset_postdata(); ?>
