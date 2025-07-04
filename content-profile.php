<?php
$profile_id = get_queried_object_id();
$bio        = get_field('bio', $profile_id);
$portrait   = get_field('portrait_image', $profile_id);
$img_url    = $portrait ? $portrait['sizes']['thumbnail'] : '';
$wiki_slug  = get_field('wikipedia_slug', $profile_id);

// Function to fetch Wikipedia summary
function get_wikipedia_intro($slug) {
    $api_url = "https://en.wikipedia.org/api/rest_v1/page/summary/" . urlencode($slug);
    $response = wp_remote_get($api_url);
    if (is_wp_error($response)) return false;
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    return !empty($data['extract']) ? esc_html($data['extract']) : false;
}
?>

<div class="person-content">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo get_the_title($profile_id); ?>" class="author-thumbnail">
  <?php endif; ?>

  <h1><?php echo get_the_title($profile_id); ?></h1>

  <div class="person-bio">
    <?php
      if ($bio) {
        echo wp_kses_post($bio);
      } elseif ($wiki_slug) {
        $wiki_intro = get_wikipedia_intro($wiki_slug);
        if ($wiki_intro) {
          echo '<p>' . esc_html($wiki_intro) . '</p>';
        }
      }
    ?>
  </div>

  <!-- 🆕 Always output editor content here, for Cover Blocks etc. -->
  <div class="person-editor-content">
    <?php the_content(); ?>
  </div>

  <?php
  // Query books where this profile is set as 'author_profile'
  $book_query = new WP_Query([
    'post_type'      => 'book',
    'posts_per_page' => -1,
    'meta_query'     => [
      [
        'key'     => 'author_profile',
        'value'   => $profile_id,
        'compare' => '='
      ]
    ]
  ]);
  ?>

  <?php if ($book_query->have_posts()): ?>
    <div class="profile-books">
      <h2>Books by <?php echo get_the_title($profile_id); ?></h2>
      <ul class="profile-book-grid">
        <?php while ($book_query->have_posts()): $book_query->the_post();
          $cover = get_field('cover_image');
          $img = $cover ? $cover['sizes']['medium'] : '';
        ?>
          <li class="profile-book-item">
            <a href="<?php the_permalink(); ?>" class="profile-book-link">
              <?php if ($img): ?>
                <img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>" class="profile-book-cover">
              <?php endif; ?>
              <div class="profile-book-title"><?php the_title(); ?></div>
            </a>
          </li>
        <?php endwhile; ?>
      </ul>
    </div>
    <?php wp_reset_postdata(); ?>
  <?php endif; ?>

  <?php get_template_part('content/profile-nav'); ?>
</div>
