<?php
$related = get_field('related_concepts'); // ACF relationship field
?>

<div class="concept-content" style="text-align:center;">

  <div class="concept-definition">

  <?php get_template_part('template-parts/navigation/concept'); ?>

  <BR>

    <?php the_content(); ?>
  </div>

   <?php if ($related): ?>
  <div class="related-concepts" style="margin-top:2em; text-align:center;">
    <h2 style="font-size:1.6em;">Related:</h2>
    <div class="tag-bubbles" style="margin-top:1em;">
      <?php foreach ($related as $item): ?>
        <span class="bubble-wrapper">
          <a class="tag-bubble" href="<?php echo get_permalink($item->ID); ?>">
            <?php echo esc_html(get_the_title($item->ID)); ?>
          </a>
        </span>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

    <?php show_featured_in_threads('concepts_referenced'); ?>

<?php

$topic = kp_find_topic(get_the_title());

if ($topic) {

    kp_render_taxonomy_knowledge($topic);

}

?>

</div>