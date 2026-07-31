<?php
/**
 * Unified Excerpt Display (passive)
 *
 * Standardized contract:
 * - items       => array
 * - query       => WP_Query|null
 * - info        => [ 'title' => '', 'emoji' => '', 'type' => '' ]
 * - search_term => string
 */
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$info         = $args['info'] ?? [];
$search_term  = $args['search_term'] ?? '';

$title = $info['title'] ?? $args['title'] ?? 'Excerpts';
$emoji = $info['emoji'] ?? $args['emoji'] ?? '📖';

$posts = $query instanceof WP_Query ? $query->posts : $query;

if ( (!$query instanceof WP_Query || !$query->have_posts()) && empty($items) && empty($posts) ) {
    return;
}
?>

<section class="cpt-excerpt-list-section">
  <?php if ($title): ?>
    <h2 class="cpt-excerpt-list-title">
      <?php if ($emoji) echo '<span class="emoji">' . esc_html($emoji) . '</span> '; ?>
      <?php echo esc_html($title); ?>
      <?php if ($search_term): ?>
        <span>containing “<?php echo esc_html($search_term); ?>”</span>
      <?php endif; ?>
    </h2>
  <?php endif; ?>

  <div class="cpt-excerpt-list">
    
    <!-- === NEW ARCHITECTURE: $items LOOP === -->
    <?php if (!empty($items)): ?>
      <?php foreach ($items as $item): ?>
        <?php
          // Extract normalized card data
          $image = !empty($item['image']) ? $item['image'] : '';
          $text  = !empty($item['excerpt']) ? $item['excerpt'] : '';
          
          // FIX: Treat meta strictly as a pre-formatted HTML string
          $meta_html = !empty($item['meta']) ? $item['meta'] : '';
        ?>
        <article class="cpt-excerpt-item">
          <?php if ($image): ?>
            <div class="cpt-excerpt-thumb">
              <a href="<?php echo esc_url($item['url']); ?>">
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($item['title']); ?>">
              </a>
            </div>
          <?php endif; ?>

          <div class="cpt-excerpt-card-content">
            <h3 class="cpt-excerpt-card-title">
              <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
            </h3>

            <?php if ($text): ?>
              <p class="cpt-excerpt-card-text"><?php echo esc_html(wp_trim_words($text, 40, '...')); ?></p>
            <?php endif; ?>

            <!-- FIX: Safely output the pre-formatted meta string -->
            <?php if ($meta_html): ?>
              <p class="cpt-excerpt-card-source">
                <?php echo wp_kses_post($meta_html); ?>
              </p>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>

    <!-- === LEGACY FALLBACK: WP_Query LOOP (Untouched) === -->
    <?php else: ?>
      <?php foreach ($posts as $post_obj): ?>
        <?php
          $post_id = is_object($post_obj) ? $post_obj->ID : intval($post_obj);
          $text   = get_field('excerpt_plain_text', $post_id);
          $source = get_field('excerpt_source', $post_id);
          $source_link  = $source ? get_permalink($source->ID) : '';
          $source_title = $source ? get_the_title($source->ID) : '';

          $author_name = '';
          $author_link = '';
          if ($source && get_post_type($source->ID) === 'book') {
            $author = get_field('author_profile', $source->ID);
            if ($author) {
              if (is_array($author)) { $author = reset($author); }
              $author_name = get_the_title($author->ID);
              $author_link = get_permalink($author->ID);
            }
          }

          $image = '';
          if ($source) {
              $cover = get_field('cover_image', $source->ID);
              if ($cover && is_array($cover)) {
                  $image = $cover['sizes']['medium'] ?? $cover['sizes']['thumbnail'] ?? $cover['url'] ?? '';
              } elseif (has_post_thumbnail($source->ID)) {
                  $image = get_the_post_thumbnail_url($source->ID, 'medium');
              }
          }

          if (!$image && have_rows('references', $post_id)) {
              $refs = get_field('references', $post_id);
              $first_ref = $refs[0] ?? null;
              if ($first_ref && !empty($first_ref['reference_thumbnail'])) {
                  $img = $first_ref['reference_thumbnail'];
                  if (is_array($img)) { $image = $img['url']; }
                  elseif (is_numeric($img)) { $image = wp_get_attachment_image_url($img, 'medium'); }
              }
          }

          if (!$image && has_post_thumbnail($post_id)) {
              $image = get_the_post_thumbnail_url($post_id, 'medium');
          }

          if (!$image) {
              $image = wp_get_attachment_image_url(22614, 'medium'); // Fallback
          }
        ?>
        <article class="cpt-excerpt-item">
          <?php if ($image): ?>
            <div class="cpt-excerpt-thumb">
              <a href="<?php echo esc_url($source_link ?: get_permalink($post_id)); ?>">
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($source_title ?: get_the_title($post_id)); ?>">
              </a>
            </div>
          <?php endif; ?>

          <div class="cpt-excerpt-card-content">
            <h3 class="cpt-excerpt-card-title">
              <a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html(get_the_title($post_id)); ?></a>
            </h3>

            <?php if ($text): ?>
              <p class="cpt-excerpt-card-text"><?php echo esc_html(wp_trim_words($text, 40, '...')); ?></p>
            <?php endif; ?>

            <?php if ($source): ?>
              <p class="cpt-excerpt-card-source">
                Source: <a href="<?php echo esc_url($source_link); ?>"><?php echo esc_html($source_title); ?></a>
                <?php if ($author_name): ?>
                  &nbsp;by <a href="<?php echo esc_url($author_link); ?>"><?php echo esc_html($author_name); ?></a>
                <?php endif; ?>
              </p>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
      <?php if ($query instanceof WP_Query) wp_reset_postdata(); ?>
    <?php endif; ?>
    
  </div>
</section>