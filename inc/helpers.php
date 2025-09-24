<?php
// helpers.php — custom theme functions

function show_featured_in_threads($meta_key, $post_id = null) {
  if (!$post_id) {
    $post_id = get_the_ID();
  }

  $threads = get_posts([
    'post_type'      => ['chapter', 'fragment'],
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'meta_query'     => [
      [
        'key'     => $meta_key,              // e.g. 'books_cited', 'lyrics_cited'
        'value'   => '"' . $post_id . '"',   // exact match inside ACF’s serialized array
        'compare' => 'LIKE'
      ]
    ]
  ]);

  if ($threads): ?>
    <div class="narrative-threads" style="margin-top: 4em; text-align:center;">
      <h2>Featured In</h2>
      <div class="thread-grid">
        <?php foreach ($threads as $thread):
          $thumb = get_the_post_thumbnail_url($thread->ID, 'medium');
        ?>
          <div class="thread-item">
            <a href="<?php echo get_permalink($thread->ID); ?>">
              <?php if ($thumb): ?>
                <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($thread->ID)); ?>">
              <?php endif; ?>
              <h3><?php echo esc_html(get_the_title($thread->ID)); ?></h3>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif;
}
