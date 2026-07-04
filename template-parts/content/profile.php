<?php
/**
 * Profile Page Template (Optimized)
 */

$profile_id = get_queried_object_id();
$bio        = get_field('bio', $profile_id);
$portrait   = get_field('portrait_image', $profile_id);
$img_url    = $portrait ? $portrait['sizes']['thumbnail'] : '';
$wiki_slug  = get_field('wikipedia_slug', $profile_id);

/* ----------------------------------------------------
   Gather all authored content
----------------------------------------------------- */

// Books authored by this profile
$books = get_posts([
    'post_type'      => 'book',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'meta_query'     => [
        [
            'key'     => 'author_profile',
            'value'   => $profile_id,
            'compare' => '='
        ]
    ]
]);

// References authored by this profile (IDs for source lookups)
$reference_ids = get_posts([
    'post_type'      => 'reference',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'meta_query'     => [
        [
            'key'     => 'author_profile',
            'value'   => $profile_id,
            'compare' => '='
        ]
    ]
]);

// Combined source list for quote_source / excerpt_source
$sources = array_unique(array_merge($books ?: [], $reference_ids ?: []));

/* ----------------------------------------------------
   Fetch Quotes (from books, from references, or directly)
----------------------------------------------------- */

$quote_meta = ['relation' => 'OR'];

if (!empty($sources)) {
    $quote_meta[] = [
        'key'     => 'quote_source',
        'value'   => $sources,
        'compare' => 'IN'
    ];
}

// Also allow direct author assignment
$quote_meta[] = [
    'key'     => 'author_profile',
    'value'   => $profile_id,
    'compare' => '='
];

$quote_meta[] = [
    'key'     => 'related_profiles',
    'value'   => '"' . $profile_id . '"',
    'compare' => 'LIKE'
];

$quotes = get_posts([
    'post_type'      => 'quote',
    'posts_per_page' => -1,
    'meta_query'     => $quote_meta,
    'orderby'        => 'title',
    'order'          => 'ASC'
]);

/* ----------------------------------------------------
   Fetch Excerpts (from books, from references, or directly)
----------------------------------------------------- */

$excerpt_meta = ['relation' => 'OR'];

if (!empty($sources)) {
    $excerpt_meta[] = [
        'key'     => 'excerpt_source',
        'value'   => $sources,
        'compare' => 'IN'
    ];
}

// Allow direct author assignment
$excerpt_meta[] = [
    'key'     => 'author_profile',
    'value'   => $profile_id,
    'compare' => '='
];

$excerpt_meta[] = [
    'key'     => 'related_profiles',
    'value'   => '"' . $profile_id . '"',
    'compare' => 'LIKE'
];

$excerpts = get_posts([
    'post_type'      => 'excerpt',
    'posts_per_page' => -1,
    'meta_query'     => $excerpt_meta,
    'orderby'        => 'title',
    'order'          => 'ASC'
]);

/* ----------------------------------------------------
   Fetch Reference content (full posts, with thumbnails)
----------------------------------------------------- */

$references = get_posts([
    'post_type'      => 'reference',
    'posts_per_page' => -1,
    'meta_query'     => [
        [
            'key'     => 'author_profile',
            'value'   => $profile_id,
            'compare' => '='
        ]
    ],
    'orderby' => 'date',
    'order'   => 'DESC'
]);

// Unique filter for quotes/excerpts to prevent duplicates
$seen_quotes = [];
$unique_quotes = array_filter($quotes, function($q) use (&$seen_quotes) {
    if (in_array($q->ID, $seen_quotes)) return false;
    $seen_quotes[] = $q->ID;
    return true;
});

$seen_excerpts = [];
$unique_excerpts = array_filter($excerpts, function($e) use (&$seen_excerpts) {
    if (in_array($e->ID, $seen_excerpts)) return false;
    $seen_excerpts[] = $e->ID;
    return true;
});

?>

<div class="person-content">

  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr(get_the_title($profile_id)); ?>" class="author-thumbnail">
  <?php endif; ?>

  <h1><?php echo esc_html(get_the_title($profile_id)); ?></h1>

  <div class="person-bio">
    <?php
    if ($bio) {
        echo wp_kses_post($bio);
    } elseif ($wiki_slug) {
        $wiki_intro = kp_get_wikipedia_intro($wiki_slug);
        if ($wiki_intro) {
            echo '<p>' . esc_html($wiki_intro) . '</p>';
        }
    }
    ?>
  </div>

  <?php
  // === Quotes (using unified renderer) ===
  if (!empty($unique_quotes)) {
      get_template_part('template-parts/render/content-objects', null, ['posts' => $unique_quotes, 'title' => 'Quotes']);
  }

  // === Excerpts (using unified renderer) ===
  if (!empty($unique_excerpts)) {
      get_template_part('template-parts/render/content-objects', null, ['posts' => $unique_excerpts, 'title' => 'Excerpts']);
  }

  // === References Grid ===
  if (!empty($references)): ?>
    <div class="profile-references" style="margin-top:3em; margin-bottom:3em; text-align:center;">
      <h2>Content by <?php echo esc_html(get_the_title($profile_id)); ?></h2>

      <ul class="profile-reference-grid" style="list-style:none; padding:0; display:flex; flex-wrap:wrap; gap:20px; justify-content:center;">
        <?php foreach ($references as $ref):
          $thumb = get_the_post_thumbnail_url($ref->ID, 'medium');
        ?>
          <li style="width:200px; text-align:center;">
            <a href="<?php echo get_permalink($ref->ID); ?>" style="text-decoration:none; color:inherit;">
              <?php if ($thumb): ?>
                <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($ref->ID)); ?>" style="width:100%; border-radius:6px;">
              <?php endif; ?>
              <div style="margin-top:0.5em; font-weight:bold;">
                <?php echo esc_html(get_the_title($ref->ID)); ?>
              </div>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif;

  // === Books Grid ===
  if (!empty($books)):
    $book_query = new WP_Query([
        'post_type'      => 'book',
        'posts_per_page' => -1,
        'post__in'       => $books,
        'orderby'        => 'title',
        'order'          => 'ASC'
    ]);

    if ($book_query->have_posts()): ?>
      <div class="profile-books">
        <h2>Books by <?php echo esc_html(get_the_title($profile_id)); ?></h2>
        <ul class="profile-book-grid">
          <?php while ($book_query->have_posts()): $book_query->the_post();
            $cover = get_field('cover_image');
            $img   = $cover ? $cover['sizes']['medium'] : '';
          ?>
            <li>
              <a href="<?php the_permalink(); ?>">
                <?php if ($img): ?>
                  <img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>">
                <?php endif; ?>
                <div><?php the_title(); ?></div>
              </a>
            </li>
          <?php endwhile; ?>
        </ul>
      </div>
      <?php wp_reset_postdata(); ?>
    <?php endif;
  endif;

  // === Featured in threads ===
  show_featured_in_threads('people_referenced');

  // === Profile navigation ===
  get_template_part('content/profile-nav');
  ?>

</div>   <!-- end person-content -->