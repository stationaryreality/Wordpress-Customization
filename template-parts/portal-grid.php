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
  TOPIC PORTALS
  =========================================
  */

  if (has_term('', 'topic', $post_id)) {
    $topics[] = get_post();
  }

  /*
  =========================================
  THEME PORTALS
  =========================================
  */

  if (has_term('', 'theme', $post_id)) {
    $themes[] = get_post();
  }
}

wp_reset_postdata();

?>

<?php if (!empty($topics)) : ?>

<section class="cpt-section portal-grid" style="margin-bottom:4rem;">

  <div style="text-align:center; margin-bottom:1.75rem;">

    <h2 style="margin-bottom:.5rem;">
      🧩 Topic Portals
    </h2>

    <p style="
      color:#666;
      max-width:760px;
      margin:0 auto 1rem auto;
      line-height:1.7;
    ">
      Fully developed topic hubs that organize major subjects and conceptual ecosystems across the site.
    </p>

    <a
      href="<?php echo esc_url(site_url('/topics')); ?>"
      style="
      display:inline-block;
      padding:.55rem 1rem;
      background:#f2f2f2;
      border-radius:999px;
      text-decoration:none;
      color:#333;
      font-weight:600;
      font-size:.95rem;
      "
    >
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

<section class="cpt-section portal-grid" style="margin-bottom:4rem;">

  <div style="text-align:center; margin-bottom:1.75rem;">

    <h2 style="margin-bottom:.5rem;">
      🎨 Theme Portals
    </h2>

    <p style="
      color:#666;
      max-width:760px;
      margin:0 auto 1rem auto;
      line-height:1.7;
    ">
      Symbolic and poetic hubs that gather recurring motifs, emotional structures,
      aesthetics, and thematic patterns from across the site.
    </p>

    <a
      href="<?php echo esc_url(site_url('/themes')); ?>"
      style="
      display:inline-block;
      padding:.55rem 1rem;
      background:#f2f2f2;
      border-radius:999px;
      text-decoration:none;
      color:#333;
      font-weight:600;
      font-size:.95rem;
      "
    >
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