<?php
/**
 * Grid partial for displaying songs in search or related queries.
 * Expects $args['query'], $args['info'], $args['search_term'].
 */

$query       = $args['query'] ?? null;
$info        = $args['info'] ?? [];
$search_term = $args['search_term'] ?? '';

if (!$query || !$query->have_posts()) return;
?>

<section style="margin-bottom:4rem;">
  <h2>
    <?php echo esc_html($info['emoji'] ?? '🎵'); ?>
    <?php echo esc_html($info['title'] ?? 'Songs'); ?>
    <?php if ($search_term): ?>
      containing “<?php echo esc_html($search_term); ?>”
    <?php endif; ?>
  </h2>

  <div class="cited-grid">
    <?php while ($query->have_posts()): $query->the_post();
      $cover   = get_field('cover_image');
      $img_url = $cover
        ? $cover['sizes']['medium']
        : get_the_post_thumbnail_url(get_the_ID(), 'medium');
    ?>
      <div class="cited-item">
        <a href="<?php the_permalink(); ?>">
          <?php if ($img_url): ?>
            <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>">
          <?php endif; ?>
          <h3><?php the_title(); ?></h3>
        </a>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<?php wp_reset_postdata(); ?>
