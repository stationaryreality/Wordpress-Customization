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

  <?php
    $args = [
      'post_type' => ['artist', 'chapter', 'quote', 'song', 'concept', 'movie', 'book', 'person'],
      'tax_query' => [[
        'taxonomy' => 'theme',
        'field'    => 'slug',
        'terms'    => $term->slug,
      ]],
      'posts_per_page' => -1,
    ];
    $query = new WP_Query($args);
  ?>

  <?php if ($query->have_posts()) : ?>
    <ul class="space-y-6">
      <?php while ($query->have_posts()) : $query->the_post(); ?>
        <li class="border-b pb-4">
          <div class="text-sm text-gray-500 uppercase tracking-wide mb-1">
            <?php echo get_post_type_object(get_post_type())->labels->singular_name; ?>
          </div>
          <a href="<?php the_permalink(); ?>" class="text-xl font-semibold hover:underline block">
            <?php the_title(); ?>
          </a>
          <p class="text-gray-600 mt-1"><?php echo wp_trim_words(get_the_excerpt(), 25); ?></p>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else : ?>
    <p>No content has been tagged with this theme yet.</p>
  <?php endif; ?>

  <?php wp_reset_postdata(); ?>
</main>

<?php get_footer(); ?>
