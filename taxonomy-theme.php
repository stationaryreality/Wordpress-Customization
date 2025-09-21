<?php get_header(); ?>

<main class="site-main max-w-screen-lg mx-auto p-6">
  <?php $term = get_queried_object(); ?>

  <section class="mb-8">
    <h1 class="text-4xl font-bold"><?php single_term_title(); ?></h1>
    <?php if (term_description()) : ?>
      <div class="mt-4 text-gray-600">
        <?php echo term_description(); ?>
      </div>
    <?php endif; ?>
  </section>

  <!-- Songs Grid -->
  <?php
    $songs = new WP_Query([
      'post_type'      => 'song',
      'posts_per_page' => -1,
      'tax_query'      => [[
        'taxonomy' => 'theme',
        'field'    => 'slug',
        'terms'    => $term->slug,
      ]],
    ]);

    if ($songs->have_posts()) : ?>
      <h2 class="text-2xl font-bold mb-4">Songs</h2>
      <div class="cited-grid">
        <?php while ($songs->have_posts()) : $songs->the_post();
          $thumb_url = '';
          if (has_post_thumbnail()) {
            $thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
          } elseif (get_field('cover_image')) {
            $cover = get_field('cover_image');
            $thumb_url = $cover['sizes']['medium'] ?? '';
          } elseif (get_field('portrait_image')) {
            $portrait = get_field('portrait_image');
            $thumb_url = $portrait['sizes']['medium'] ?? '';
          } elseif (get_field('image_file')) {
            $image_file = get_field('image_file');
            $thumb_url = $image_file['sizes']['medium'] ?? '';
          }
        ?>
          <div class="cited-item">
            <a href="<?php the_permalink(); ?>">
              <?php if ($thumb_url): ?>
                <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title(); ?>">
              <?php endif; ?>
              <h3><?php the_title(); ?></h3>
            </a>
          </div>
        <?php endwhile; ?>
      </div>
    <?php endif; wp_reset_postdata(); ?>


  <!-- Narrative Threads Grid (Chapters) -->
  <?php
    $chapters = new WP_Query([
      'post_type'      => ['chapter', 'fragment'],
      'posts_per_page' => -1,
      'tax_query'      => [[
        'taxonomy' => 'theme',
        'field'    => 'slug',
        'terms'    => $term->slug,
      ]],
    ]);

    if ($chapters->have_posts()) : ?>
      <div class="narrative-threads mt-12">
        <h2>
          Themed Narratives<?php echo $chapters->found_posts > 1 ? 's' : ''; ?>
        </h2>
        <div class="thread-grid">
          <?php while ($chapters->have_posts()) : $chapters->the_post();
            $thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium');
          ?>
            <div class="thread-item">
              <a href="<?php the_permalink(); ?>">
                <?php if ($thumb): ?>
                  <img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title(); ?>">
                <?php endif; ?>
                <h3><?php the_title(); ?></h3>
              </a>
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    <?php endif; wp_reset_postdata(); ?>

</main>

<?php get_footer(); ?>
