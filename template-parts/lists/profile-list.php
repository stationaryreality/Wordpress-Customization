<?php
/**
 * Shared Profile List Template
 *
 * Expects:
 * - query       => WP_Query object
 * - title       => Section/page title
 * - emoji       => Emoji (optional, from centralized lookup)
 * - search_term => Optional (only used on search)
 */
$query       = $args['query'];
$title       = $args['title'] ?? 'People Referenced';
$emoji       = $args['emoji'] ?? '';
$search_term = $args['search_term'] ?? '';

if (!$query->have_posts()) return;
?>

<section class="profile-list container" style="max-width: 800px; margin: auto; padding: 2rem 1rem;">
  <h1>
    <?php if ($emoji) echo $emoji . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term) : ?>
      containing “<?php echo esc_html($search_term); ?>”
    <?php endif; ?>
  </h1>
  <p class="intro-text">People referenced across the archive.</p>

  <div class="profile-items">
    <?php while ($query->have_posts()) : $query->the_post(); ?>
      <?php
        $portrait = get_field('portrait_image', get_the_ID()); // ACF image array
        $img_url  = $portrait ? $portrait['sizes']['thumbnail'] : '';
      ?>
      <div class="profile-entry" style="display:flex; align-items:flex-start; gap:1rem; margin-bottom:2rem; border-bottom:1px solid #ddd; padding-bottom:1rem;">
        <?php if ($img_url): ?>
          <a href="<?php the_permalink(); ?>" class="profile-thumb">
            <img src="<?php echo esc_url($img_url); ?>" 
                 alt="<?php echo esc_attr(get_the_title()); ?>" 
                 style="width:48px; height:48px; border-radius:50%; object-fit:cover;">
          </a>
        <?php endif; ?>

        <div class="profile-text">
          <h2 style="margin-bottom:0.5rem;">
            <a href="<?php the_permalink(); ?>">
              <?php the_title(); ?>
            </a>
          </h2>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</section>
<?php wp_reset_postdata(); ?>
