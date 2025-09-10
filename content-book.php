<?php
get_header();

if (have_posts()) :
  while (have_posts()) : the_post();

    $book_id        = get_the_ID();
    $author_profile = get_field('author_profile');
    $summary        = get_field('summary');
    $wiki_slug      = get_field('wikipedia_slug');
    $cover          = get_field('cover_image');
    $img_url        = $cover ? $cover['sizes']['medium'] : '';

    // Wikipedia summary fetcher
    function get_book_wikipedia_intro($slug) {
      $api_url = "https://en.wikipedia.org/api/rest_v1/page/summary/" . urlencode($slug);
      $response = wp_remote_get($api_url);
      if (is_wp_error($response)) return false;
      $body = wp_remote_retrieve_body($response);
      $data = json_decode($body, true);
      return !empty($data['extract']) ? esc_html($data['extract']) : false;
    }
?>

<div class="book-content centered">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="book-cover">
  <?php endif; ?>

  <h1 class="book-title"><?php the_title(); ?></h1>

  <?php if ($subtitle = get_field('subtitle')): ?>
    <h2 class="book-subtitle"><?php echo esc_html($subtitle); ?></h2>
  <?php endif; ?>

  <div class="book-description">
    <?php
      if ($summary) {
        echo wp_kses_post($summary);
      } elseif ($wiki_slug) {
        $wiki_intro = get_book_wikipedia_intro($wiki_slug);
        if ($wiki_intro) {
          echo '<p>' . esc_html($wiki_intro) . '</p>';
        }
      } else {
        the_content();
      }
    ?>
  </div>
</div>


  <?php if ($author_profile): ?>
    <?php
      $portrait     = get_field('portrait_image', $author_profile->ID);
      $thumb        = $portrait ? $portrait['sizes']['thumbnail'] : '';
      $bio          = get_field('bio', $author_profile->ID);
      $profile_slug = get_field('wikipedia_slug', $author_profile->ID);
    ?>
    <div class="book-author" style="margin-top: 2em;">
      <?php if ($thumb): ?>
        <a href="<?php echo get_permalink($author_profile->ID); ?>" class="author-link">
          <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($author_profile->ID)); ?>" class="author-thumbnail rounded">
          <h3>By <?php echo esc_html(get_the_title($author_profile->ID)); ?></h3>
        </a>
      <?php else: ?>
        <h3>By <a href="<?php echo get_permalink($author_profile->ID); ?>">
          <?php echo esc_html(get_the_title($author_profile->ID)); ?>
        </a></h3>
      <?php endif; ?>

      <div class="author-bio" style="margin-top: 1em;">
        <?php
          if ($bio) {
            echo wp_kses_post($bio);
          } elseif ($profile_slug) {
            $wiki_intro = get_book_wikipedia_intro($profile_slug);
            if ($wiki_intro) {
              echo '<p>' . esc_html($wiki_intro) . '</p>';
            }
          }
        ?>
      </div>
    </div>
  <?php endif; ?>


    <?php
    // === Related Quotes (from quote CPT) ===
    $quotes = get_posts([
      'post_type'      => 'quote',
      'posts_per_page' => -1,
      'meta_query'     => [
        [
          'key'     => 'quote_source',
          'value'   => $book_id,
          'compare' => '='
        ]
      ]
    ]);

    if ($quotes): ?>
      <div class="related-quotes" style="margin-top:3em; text-align:center;">
        <h2>Quotes</h2>
        <ul style="list-style:none; padding:0; display:inline-block; text-align:left;">
          <?php foreach ($quotes as $quote): ?>
            <li>
              <a href="<?php echo get_permalink($quote->ID); ?>">
                <?php echo esc_html(get_the_title($quote->ID)); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
  <?php endif; ?>


  <?php
    // === Related Excerpts (from excerpt CPT) ===
    $excerpts = get_posts([
      'post_type'      => 'excerpt',
      'posts_per_page' => -1,
      'meta_query'     => [
        [
          'key'     => 'excerpt_source',
          'value'   => $book_id,
          'compare' => '='
        ]
      ]
    ]);

    if ($excerpts): ?>
      <div class="related-excerpts" style="margin-top:3em; text-align:center;">
        <h2>Excerpts</h2>
        <ul style="list-style:none; padding:0; display:inline-block; text-align:left;">
          <?php foreach ($excerpts as $excerpt): ?>
            <li>
              <a href="<?php echo get_permalink($excerpt->ID); ?>">
                <?php echo esc_html(get_the_title($excerpt->ID)); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
  <?php endif; ?>


  <?php
    // === Narrative Threads ===
    $threads = get_posts([
      'post_type'      => 'chapter',
      'posts_per_page' => -1,
      'orderby'        => 'menu_order',
      'order'          => 'ASC',
      'meta_query'     => [
        [
          'key'     => 'books_cited', // Assuming it's a relationship field
          'value'   => $book_id,
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
  <?php endif; ?>

  <?php get_template_part('content/book-nav'); ?>
</div>

<?php
  endwhile;
endif;

get_footer();
?>
