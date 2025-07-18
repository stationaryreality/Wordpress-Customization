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

  <?php if ($author_profile): ?>
    <?php
      $portrait    = get_field('portrait_image', $author_profile->ID);
      $thumb       = $portrait ? $portrait['sizes']['thumbnail'] : '';
      $bio         = get_field('bio', $author_profile->ID);
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

    <?php
      // Book Quotes (ACF repeater or clone field on author_profile)
      $quotes = get_field('quotes_from_this_book', $author_profile->ID); // You can adjust the field name
      if ($quotes):
        echo '<div class="book-quotes" style="margin-top: 3em;">';
        echo '<h2>Quotes from This Book</h2>';
        foreach ($quotes as $quote) {
          echo '<div class="quote-block">';
          echo $quote['quote_html_block']; // assuming this is raw HTML block
          echo '</div>';
        }
        echo '</div>';
      endif;
    ?>
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
      <div class="narrative-threads" style="margin-top: 4em;">
        <h2>Narrative Threads Featuring <?php the_title(); ?></h2>
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
