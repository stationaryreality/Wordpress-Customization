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
   Fetch Wikipedia intro (fallback)
----------------------------------------------------- */
function get_wikipedia_intro($slug) {
    $api_url = "https://en.wikipedia.org/api/rest_v1/page/summary/" . urlencode($slug);
    $response = wp_remote_get($api_url);
    if (is_wp_error($response)) return false;
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    return !empty($data['extract']) ? esc_html($data['extract']) : false;
}

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
        if ($wiki_intro = get_wikipedia_intro($wiki_slug)) {
          echo '<p>' . esc_html($wiki_intro) . '</p>';
        }
      }
    ?>
  </div>

  <div class="person-editor-content">
    <?php the_content(); ?>
  </div>

  <!-- ======================
       QUOTES
  ======================= -->
  <?php if ($quotes): ?>
    <div class="profile-quotes" style="margin-top:3em; margin-bottom:3em; text-align:center;">
      <h2>Quotes</h2>
      <ul style="list-style:none; padding:0; margin:0;">
        <?php 
        $seen = [];
        foreach ($quotes as $quote):
          if (in_array($quote->ID, $seen, true)) continue;
          $seen[] = $quote->ID;
        ?>
          <li style="margin:0.5em 0;">
            <a href="<?php echo get_permalink($quote->ID); ?>">
              <?php echo esc_html(get_the_title($quote->ID)); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- ======================
       EXCERPTS
  ======================= -->
  <?php if ($excerpts): ?>
    <div class="profile-excerpts" style="margin-top:3em; margin-bottom:3em; text-align:center;">
      <h2>Excerpts</h2>
      <ul style="list-style:none; padding:0; margin:0;">
        <?php 
        $seen = [];
        foreach ($excerpts as $excerpt):
          if (in_array($excerpt->ID, $seen, true)) continue;
          $seen[] = $excerpt->ID;
        ?>
          <li style="margin:0.5em 0;">
            <a href="<?php echo get_permalink($excerpt->ID); ?>">
              <?php echo esc_html(get_the_title($excerpt->ID)); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- ======================
       REFERENCES GRID
  ======================= -->
  <?php if ($references): ?>
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
  <?php endif; ?>

  <!-- ======================
       BOOK GRID
  ======================= -->
  <?php
  $book_query = (!empty($books))
    ? new WP_Query([
        'post_type'      => 'book',
        'posts_per_page' => -1,
        'post__in'       => $books,
        'orderby'        => 'title',
        'order'          => 'ASC'
      ])
    : false;
  ?>

  <?php if ($book_query && $book_query->have_posts()): ?>
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
  <?php endif; ?>

  <?php get_template_part('content/profile-nav'); ?>

</div>
