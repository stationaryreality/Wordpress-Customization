<?php
$definition = get_field('definition');
$portrait   = get_field('portrait_image', get_the_ID());
$img_url    = $portrait ? $portrait['sizes']['thumbnail'] : '';
$related    = get_field('related_concepts'); // ACF relationship field, return format: Post Object
?>

<div class="person-content">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="author-thumbnail">
  <?php endif; ?>

  <h1><?php the_title(); ?></h1>

  <div class="person-bio">
    <?php if ($definition): ?>
      <?php echo wp_kses_post($definition); ?>
    <?php else: ?>
      <?php the_content(); ?>
    <?php endif; ?>
  </div>

  <?php if ($related): ?>
    <div class="related-concepts mt-6">
      <h2 class="text-xl font-semibold mb-2">🔁 Related Concepts</h2>
      <ul class="list-disc list-inside">
        <?php foreach ($related as $item): ?>
          <li>
            <a href="<?php echo get_permalink($item->ID); ?>" class="underline hover:text-blue-600">
              <?php echo esc_html(get_the_title($item->ID)); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php get_template_part('content/concept-nav'); ?>
</div>
