<?php
/**
 * Portal Grid Template
 * Split by whether portal has Topic or Theme terms
 */

$query = $args['query'] ?? null;

/*
=========================================
FALLBACK QUERY
=========================================
*/

if (!$query) {

  $query = new WP_Query([
    'post_type'      => 'portal',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
  ]);
}

if (!$query->have_posts()) {
  return;
}

/*
=========================================
BUCKETS
=========================================
*/

$topics = [];
$themes = [];

while ($query->have_posts()) {

  $query->the_post();

  $post_id = get_the_ID();

  /*
  =========================================
  TOPIC & THEME PORTALS
  =========================================
  */

  $topic_terms = kp_filter_system_terms(
    get_the_terms($post_id, 'topic') ?: []
  );

  $theme_terms = kp_filter_system_terms(
    get_the_terms($post_id, 'theme') ?: []
  );

  if (!empty($topic_terms)) {
    $topics[] = get_post();
  }

  if (!empty($theme_terms)) {
    $themes[] = get_post();
  }
}

wp_reset_postdata();

?>

<?php if (!empty($topics)) : ?>

<section class="cpt-section portal-grid">

  <div class="portal-grid__header">

    <h2 class="portal-grid__title">
      🧩 Topic Portals
    </h2>

    <p class="portal-grid__description">
      Fully developed topic hubs that organize major subjects and conceptual ecosystems across the site.
    </p>

    <a href="<?php echo esc_url(site_url('/topics')); ?>" class="portal-grid__cta">
      → Browse Full Topics Directory
    </a>

  </div>

  <div class="tag-posts-grid">

    <?php foreach ($topics as $post) : setup_postdata($post); ?>

      <div class="tag-post-item">

        <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">

          <?php if (has_post_thumbnail()) : ?>

            <img
              src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium')); ?>"
              alt="<?php the_title_attribute(); ?>"
            >

          <?php endif; ?>

        </a>

        <a href="<?php the_permalink(); ?>" class="tag-post-title">
          <?php the_title(); ?>
        </a>

        <p class="tag-post-excerpt">
          <?php echo get_the_excerpt(); ?>
        </p>

      </div>

    <?php endforeach; ?>

  </div>

</section>

<?php endif; ?>

<?php if (!empty($themes)) : ?>

<section class="cpt-section portal-grid">

  <div class="portal-grid__header">

    <h2 class="portal-grid__title">
      🎨 Theme Portals
    </h2>

    <p class="portal-grid__description">
      Symbolic and poetic hubs that gather recurring motifs, emotional structures,
      aesthetics, and thematic patterns from across the site.
    </p>

    <a href="<?php echo esc_url(site_url('/themes')); ?>" class="portal-grid__cta">
      → Browse Full Themes Directory
    </a>

  </div>

  <div class="tag-posts-grid">

    <?php foreach ($themes as $post) : setup_postdata($post); ?>

      <div class="tag-post-item">

        <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">

          <?php if (has_post_thumbnail()) : ?>

            <img
              src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium')); ?>"
              alt="<?php the_title_attribute(); ?>"
            >

          <?php endif; ?>

        </a>

        <a href="<?php the_permalink(); ?>" class="tag-post-title">
          <?php the_title(); ?>
        </a>

        <p class="tag-post-excerpt">
          <?php echo get_the_excerpt(); ?>
        </p>

      </div>

    <?php endforeach; ?>

  </div>

</section>

<?php endif; ?>

<?php wp_reset_postdata(); ?>