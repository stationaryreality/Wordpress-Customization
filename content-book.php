<?php
$book_id        = get_the_ID();
$author_profile = get_field('author_profile');
$summary        = get_field('summary');
$wiki_slug      = get_field('wikipedia_slug');
$cover          = get_field('cover_image');
$img_url        = $cover ? $cover['sizes']['medium'] : '';

/**
 * Wikipedia (shared helper)
 * NOTE: now assumed to come from /inc/helpers/wikipedia.php
 */
?>

<div class="book-content centered">

  <!-- =========================
       COVER
  ========================== -->
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="book-cover">
  <?php endif; ?>

  <h1 class="book-title"><?php the_title(); ?></h1>

  <?php if ($subtitle = get_field('subtitle')): ?>
    <h2 class="book-subtitle"><?php echo esc_html($subtitle); ?></h2>
  <?php endif; ?>

  <!-- =========================
       DESCRIPTION
  ========================== -->
    <div class="book-description">

        <?php
        if ($summary) {
            echo wp_kses_post($summary);

        } elseif ($wiki_slug && function_exists('kp_get_wikipedia_intro')) {

            // SAFE shared Wikipedia call
            $wiki = kp_get_wikipedia_intro($wiki_slug);

            if ($wiki) {
                echo '<p>' . esc_html($wiki) . '</p>';
            }

        } else {
            the_content();
        }
        ?>

    </div>

  <!-- =========================
       AUTHOR
  ========================== -->
  <?php if ($author_profile): ?>

    <?php
      $portrait = get_field('portrait_image', $author_profile->ID);
      $thumb    = $portrait ? $portrait['sizes']['thumbnail'] : '';
      $bio      = get_field('bio', $author_profile->ID);
      $profile_slug = get_field('wikipedia_slug', $author_profile->ID);
    ?>

    <div class="book-author" style="margin-top:2em;">

      <a href="<?php echo get_permalink($author_profile->ID); ?>" class="author-link">

        <?php if ($thumb): ?>
          <img src="<?php echo esc_url($thumb); ?>" class="author-thumbnail rounded" alt="">
        <?php endif; ?>

        <h3>By <?php echo esc_html(get_the_title($author_profile->ID)); ?></h3>

      </a>

      <div class="author-bio" style="margin-top:1em;">

        <?php if ($bio): ?>

          <?php echo wp_kses_post($bio); ?>

        <?php elseif ($profile_slug && function_exists('get_wikipedia_intro')): ?>

          <?php
            $wiki_intro = get_wikipedia_intro($profile_slug);
            if ($wiki_intro) {
              echo '<p>' . esc_html($wiki_intro) . '</p>';
            }
          ?>

        <?php endif; ?>

      </div>
    </div>

  <?php endif; ?>


<!-- =====================================================
     RELATED CONTENT (SAFE LIMITS - PREVENT FREEZES)
====================================================== -->

<?php
// ================= QUOTES =================
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
?>

<?php if ($quotes): ?>
    <?php get_template_part('template-parts/render/content-objects', null, ['posts' => $quotes, 'title' => 'Quotes']); ?>
<?php endif; ?>

<?php
// ================= EXCERPTS =================
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
?>

<?php if ($excerpts): ?>
    <?php get_template_part('template-parts/render/content-objects', null, ['posts' => $excerpts, 'title' => 'Excerpts']); ?>
<?php endif; ?>


  <!-- =========================
       FEATURED THREADS
  ========================== -->
  <?php show_featured_in_threads('books_cited'); ?>

  <!-- =========================
       NAV
  ========================== -->
  <?php get_template_part('content/book-nav'); ?>

</div>