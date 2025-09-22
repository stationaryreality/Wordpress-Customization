<?php
/**
 * Book Grid Template
 * Reusable grid for displaying book CPTs
 *
 * Expected args:
 * - query: WP_Query object (optional, if not passed it will run its own)
 * - title: Section title (optional)
 * - emoji: Emoji for section title (optional)
 * - search_term: Current search term (optional, for search context)
 */

$query       = $args['query'] ?? null;
$section_title = $args['title'] ?? '';
$emoji       = $args['emoji'] ?? '';
$search_term = $args['search_term'] ?? '';

// If no query is passed, build one for all books
if (!$query) {
  $query = new WP_Query([
    'post_type'      => 'book',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
  ]);
}

if (!$query->have_posts()) return;
?>

<section class="cpt-section book-grid" style="margin-bottom:4rem;">
  <?php if ($section_title): ?>
    <h2>
      <?php echo esc_html(trim($emoji . ' ' . $section_title)); ?>
      <?php if ($search_term): ?>
        containing “<?php echo esc_html($search_term); ?>”
      <?php endif; ?>
    </h2>
  <?php endif; ?>

  <div class="cited-grid">
    <?php while ($query->have_posts()): $query->the_post();
      $author   = get_field('author');
      $cover    = get_field('cover_image');
      $img_url  = $cover ? $cover['sizes']['medium'] : '';
    ?>
      <div class="cited-item">
        <a href="<?php the_permalink(); ?>">
          <?php if ($img_url): ?>
            <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title_attribute(); ?>">
          <?php endif; ?>
          <h3><?php the_title(); ?></h3>
        </a>
        <?php if ($author): ?>
          <p><strong><?php echo esc_html($author); ?></strong></p>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<?php wp_reset_postdata(); ?>
