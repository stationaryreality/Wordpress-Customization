<?php
$related = get_field('related_concepts'); // ACF relationship field
?>

<div class="concept-content" style="text-align:center;">

  <div class="concept-definition">
    <?php the_content(); ?>
  </div>

  <?php if ($related): ?>
    <div class="related-concepts" style="margin-top:2em;">
      <h2>🔁 Related Concepts</h2>
      <ul style="list-style:none; padding:0; display:inline-block; text-align:left;">
        <?php foreach ($related as $item): ?>
          <li>
            <a href="<?php echo get_permalink($item->ID); ?>">
              <?php echo esc_html(get_the_title($item->ID)); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php get_template_part('content/concept-nav'); ?>
</div>
