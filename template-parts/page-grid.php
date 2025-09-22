<?php
/* Template Part: Pages Grid */

$title          = $args['title'] ?? 'Site Resources';
$excluded_slugs = $args['excluded_slugs'] ?? array();

$excluded_ids = array();
if (!empty($excluded_slugs)) {
  $excluded_ids = get_posts(array(
    'post_type'      => 'page',
    'fields'         => 'ids',
    'post_name__in'  => $excluded_slugs,
    'posts_per_page' => -1,
  ));
}

$page_args = array(
  'post_type'      => 'page',
  'post_status'    => 'publish',
  'posts_per_page' => -1,
  'post__not_in'   => array_merge(array(get_the_ID()), $excluded_ids),
  'orderby'        => 'menu_order',
  'order'          => 'ASC'
);

$pages_query = new WP_Query($page_args);

if ($pages_query->have_posts()) : ?>
  <section style="margin-bottom:4rem;">
    <h2 class="page-section-title"><?php echo esc_html($title); ?></h2>
    <div class="tag-posts-grid">
      <?php while ($pages_query->have_posts()) : $pages_query->the_post(); ?>
        <div class="tag-post-item">
          <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
            <?php if (has_post_thumbnail()) : ?>
              <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
            <?php endif; ?>
          </a>
          <a href="<?php the_permalink(); ?>" class="tag-post-title"><?php the_title(); ?></a>
          <p class="tag-post-excerpt"><?php the_excerpt(); ?></p>
        </div>
      <?php endwhile; ?>
    </div>
  </section>
  <?php wp_reset_postdata();
endif;
?>
