<?php
/**
 * Template Part: Concept List
 * Renders concepts in a footnotes-style list (not a grid).
 *
 * Expects:
 * - query       => WP_Query or array of posts
 * - title       => (optional) section title
 * - emoji       => (optional) emoji string
 * - search_term => (optional) search keyword
 */

$query       = $args['query'] ?? null;
$title       = $args['title'] ?? '';
$emoji       = $args['emoji'] ?? '';
$search_term = $args['search_term'] ?? '';

if (!$query || !$query->have_posts()) return;
?>

<section class="concepts-section" style="margin:2em auto;max-width:800px;">
  <?php if ($title): ?>
    <h2>
      <?php if ($emoji): ?><span style="font-size:1.2em;"><?php echo esc_html($emoji); ?></span><?php endif; ?>
      <?php echo esc_html($title); ?>
      <?php if ($search_term): ?>
        containing “<?php echo esc_html($search_term); ?>”
      <?php endif; ?>
    </h2>
  <?php endif; ?>

  <ul class="concept-list" style="list-style:none;padding:0;">
    <?php while ($query->have_posts()): $query->the_post(); ?>
      <li style="display:flex;align-items:flex-start;gap:10px;margin-bottom:1em;">
        <?php if (has_post_thumbnail()): ?>
          <a href="<?php the_permalink(); ?>">
            <img src="<?php the_post_thumbnail_url('thumbnail'); ?>" alt="<?php the_title(); ?>"
              style="width:48px;height:48px;object-fit:cover;border-radius:50%;">
          </a>
        <?php endif; ?>

        <div>
          <a href="<?php the_permalink(); ?>"><strong><?php the_title(); ?></strong></a>
          <?php
          $def = get_field('definition', get_the_ID());
          if ($def): ?>
            <div style="margin-top:0.25rem;"><?php echo esc_html(wp_trim_words($def, 30)); ?></div>
          <?php endif; ?>
        </div>
      </li>
    <?php endwhile; ?>
  </ul>
</section>

<?php wp_reset_postdata(); ?>
