<?php
/**
 * Unified Quote Display (passive)
 *
 * Expects in $args:
 * - query => WP_Query or array of WP_Posts
 * - title => Section title (optional)
 * - emoji => Optional emoji
 * - search_term => Optional (only used for search displays)
 */

$query = $args['query'] ?? null;
$title = $args['title'] ?? '';
$emoji = $args['emoji'] ?? '';
$search_term = $args['search_term'] ?? '';

if (!$query) return;

// Normalize to iterable
$posts = $query instanceof WP_Query ? $query->posts : $query;
if (empty($posts)) return;
?>

<section class="portal-section quote-list-section">
  <?php if ($title): ?>
    <h2 class="portal-section-title">
      <?php if ($emoji) echo '<span class="emoji">' . esc_html($emoji) . '</span> '; ?>
      <?php echo esc_html($title); ?>
      <?php if ($search_term) : ?>
        <span class="search-note"> — containing “<?php echo esc_html($search_term); ?>”</span>
      <?php endif; ?>
    </h2>
  <?php endif; ?>

  <div class="portal-quote-list">
    <?php foreach ($posts as $post_obj):
      $post_id = is_object($post_obj) ? $post_obj->ID : intval($post_obj);

      $excerpt = get_field('quote_plain_text', $post_id);
      $source  = get_field('quote_source', $post_id);
      $source_link  = $source ? get_permalink($source->ID) : '';
      $source_title = $source ? get_the_title($source->ID) : '';

      // Author info (only if source is a book)
      $author_name = '';
      $author_link = '';
      if ($source && get_post_type($source->ID) === 'book') {
        $author = get_field('author_profile', $source->ID);
        if ($author) {
          if (is_array($author)) {
            $author = reset($author);
          }
          $author_name = get_the_title($author->ID);
          $author_link = get_permalink($author->ID);
        }
      }

      // Image resolution logic: prefer source cover, then source featured, then post featured
      $image = '';
      if ($source) {
        $cover = get_field('cover_image', $source->ID);
        if ($cover && is_array($cover)) {
          $image = $cover['sizes']['medium'] ?? ($cover['sizes']['thumbnail'] ?? ($cover['url'] ?? ''));
        } elseif (has_post_thumbnail($source->ID)) {
          $image = get_the_post_thumbnail_url($source->ID, 'medium');
        }
      }

      if (!$image && has_post_thumbnail($post_id)) {
        $image = get_the_post_thumbnail_url($post_id, 'medium');
      }
    ?>
      <article class="portal-quote-item">
        <?php if ($image): ?>
          <div class="quote-thumb">
            <a href="<?php echo esc_url($source_link ?: get_permalink($post_id)); ?>">
              <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($source_title ?: get_the_title($post_id)); ?>">
            </a>
          </div>
        <?php endif; ?>

        <div class="quote-content">
          <?php $title_text = get_the_title($post_id); if ($title_text): ?>
            <h3 class="quote-title">
              <a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html($title_text); ?></a>
            </h3>
          <?php endif; ?>

          <?php if ($excerpt): ?>
            <blockquote class="quote-excerpt"><?php echo esc_html(wp_trim_words($excerpt, 40, '...')); ?></blockquote>
          <?php endif; ?>

          <?php if ($source): ?>
            <p class="quote-source">
              from <a href="<?php echo esc_url($source_link); ?>"><?php echo esc_html($source_title); ?></a>
              <?php if ($author_name): ?>
                &nbsp;by <a href="<?php echo esc_url($author_link); ?>"><?php echo esc_html($author_name); ?></a>
              <?php endif; ?>
            </p>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<?php if ($query instanceof WP_Query) wp_reset_postdata(); ?>
