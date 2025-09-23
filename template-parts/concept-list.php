<?php
/**
 * Template Part: Concept List
 * Unified list style (matches lyrics, quotes, references, profiles).
 *
 * Expects:
 * - query       => WP_Query object
 * - title       => Section/page title
 * - emoji       => Emoji (optional)
 * - search_term => Optional search keyword
 */
$query       = $args['query'] ?? null;
$title       = $args['title'] ?? 'Concepts';
$emoji       = $args['emoji'] ?? '';
$search_term = $args['search_term'] ?? '';

if (!$query || !$query->have_posts()) return;
?>

<section class="concept-list-section container" style="max-width:800px;margin:2rem auto;padding:0 1rem;">
  <h1>
    <?php if ($emoji) echo esc_html($emoji) . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term): ?>
      containing “<?php echo esc_html($search_term); ?>”
    <?php endif; ?>
  </h1>
    <p class="intro-text">Definitions and explanations of key terms used throughout the site.</p>

  <div class="concept-list">
    <?php while ($query->have_posts()): $query->the_post(); ?>
      <?php
        $definition = get_field('definition', get_the_ID());
        $thumb_url  = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') : '';
      ?>
      <div class="concept-entry" style="display:flex;align-items:flex-start;gap:1rem;margin-bottom:2rem;border-bottom:1px solid #ddd;padding-bottom:1rem;">
        <?php if ($thumb_url): ?>
          <a href="<?php the_permalink(); ?>" class="concept-thumb">
            <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>"
                 style="width:48px;height:48px;object-fit:cover;border-radius:50%;">
          </a>
        <?php endif; ?>

        <div class="concept-text">
          <h2 style="margin-bottom:0.5rem;">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
          </h2>
          <?php if ($definition): ?>
            <p style="margin:0;"><?php echo esc_html(wp_trim_words($definition, 30, '...')); ?></p>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<?php wp_reset_postdata(); ?>
