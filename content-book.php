<?php
$book_id        = get_the_ID();
$author_profile = get_field('author_profile');
$summary        = get_field('summary');
$wiki_slug      = get_field('wikipedia_slug');
$cover          = get_field('cover_image');
$img_url        = $cover ? $cover['sizes']['medium'] : '';
$subtitle       = get_field('subtitle');

// === Description logic ===
if ($summary) {
    $description = wp_kses_post($summary);
} elseif ($wiki_slug && function_exists('kp_get_wikipedia_intro')) {
    $wiki = kp_get_wikipedia_intro($wiki_slug);
    $description = $wiki ? '<p>' . esc_html($wiki) . '</p>' : '';
} else {
    $description = wp_kses_post(get_the_content());
}

// === Author bio logic ===
$author_bio = '';
if ($author_profile) {
    $portrait = get_field('portrait_image', $author_profile->ID);
    $thumb    = $portrait ? $portrait['sizes']['thumbnail'] : '';
    $bio      = get_field('bio', $author_profile->ID);
    $profile_slug = get_field('wikipedia_slug', $author_profile->ID);

    if ($bio) {
        $author_bio = wp_kses_post($bio);
    } elseif ($profile_slug && function_exists('kp_get_wikipedia_intro')) {
        $wiki_intro = kp_get_wikipedia_intro($profile_slug);
        $author_bio = $wiki_intro ? '<p>' . esc_html($wiki_intro) . '</p>' : '';
    }
}
?>

<div class="person-content">

  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="book-cover">
  <?php endif; ?>

  <h1 class="book-title"><?php the_title(); ?></h1>

  <?php if ($subtitle): ?>
    <h2 class="book-subtitle"><?php echo esc_html($subtitle); ?></h2>
  <?php endif; ?>

  <div class="person-bio">
    <?php echo $description; ?>
  </div>

  <?php if ($author_profile): ?>
    <div class="book-author">
      <a href="<?php echo get_permalink($author_profile->ID); ?>" class="author-link">
        <?php if ($thumb): ?>
          <img src="<?php echo esc_url($thumb); ?>" class="author-thumbnail rounded" alt="">
        <?php endif; ?>
        <h3>By <?php echo esc_html(get_the_title($author_profile->ID)); ?></h3>
      </a>
      <?php if ($author_bio): ?>
        <div class="author-bio">
          <?php echo $author_bio; ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php
  // === Related Quotes ===
  $quotes = get_posts([
    'post_type'      => 'quote',
    'posts_per_page' => 20,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'meta_query'     => [
      [
        'key'     => 'quote_source',
        'value'   => $book_id,
        'compare' => '='
      ]
    ]
  ]);

  if (!empty($quotes)) {
    get_template_part('template-parts/render/content-objects', null, ['posts' => $quotes, 'title' => 'Quotes']);
  }

  // === Related Excerpts ===
  $excerpts = get_posts([
    'post_type'      => 'excerpt',
    'posts_per_page' => 20,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'meta_query'     => [
      [
        'key'     => 'excerpt_source',
        'value'   => $book_id,
        'compare' => '='
      ]
    ]
  ]);

  if (!empty($excerpts)) {
    get_template_part('template-parts/render/content-objects', null, ['posts' => $excerpts, 'title' => 'Excerpts']);
  }

  // === Featured Threads ===
  show_featured_in_threads('books_cited');
  ?>

  <?php get_template_part('content/book-nav'); ?>

</div>   <!-- end person-content -->