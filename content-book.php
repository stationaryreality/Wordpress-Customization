<?php
get_header();

if (have_posts()) :
  while (have_posts()) : the_post();

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

      <?php if ($author_profile): ?>
        <?php
        $portrait = get_field('portrait_image', $author_profile->ID);
        $thumb = $portrait ? $portrait['sizes']['thumbnail'] : '';
        ?>
        <div class="book-author">
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
        </div>
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

      <?php get_template_part('content/book-nav'); ?>
    </div>

  <?php endwhile;
endif;

get_footer();
