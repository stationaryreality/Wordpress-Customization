<?php
$related = get_field('related_concepts'); // ACF relationship field
?>

<div class="cpt-concept-content">

  <div class="cpt-concept-definition">
    <?php get_template_part('template-parts/navigation/concept'); ?>
    <br><br>
    <?php the_content(); ?>
  </div>

  <?php if ($related): ?>
    <div class="cpt-concept-related">
      <h2 class="cpt-concept-related-title">Related:</h2>
      <div class="cpt-concept-bubbles">
        <?php foreach ($related as $item): ?>
          <span class="bubble-wrapper">
            <a class="cpt-concept-bubble" href="<?php echo get_permalink($item->ID); ?>">
              <?php echo esc_html(get_the_title($item->ID)); ?>
            </a>
          </span>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <?php show_featured_in_threads('concepts_referenced'); ?>

  <?php
  $concept_title = get_the_title();
  $portal = get_page_by_title($concept_title, OBJECT, 'portal');
  $topic = $portal ? null : get_term_by('name', $concept_title, 'topic');

  if ($portal || $topic): ?>
    <section class="cpt-concept-portal-topic">
      <div class="cpt-concept-portal-grid">
        <?php if ($portal): ?>
          <div class="cpt-concept-portal-item">
            <a href="<?php echo get_permalink($portal->ID); ?>" class="cpt-concept-portal-thumb">
              <?php if (has_post_thumbnail($portal->ID)): ?>
                <img src="<?php echo esc_url(get_the_post_thumbnail_url($portal->ID, 'medium')); ?>" alt="<?php echo esc_attr(get_the_title($portal->ID)); ?>">
              <?php endif; ?>
            </a>
            <a href="<?php echo get_permalink($portal->ID); ?>" class="cpt-concept-portal-title">
              🚪 Portal Page for <?php echo esc_html($concept_title); ?>
            </a>
            <p class="cpt-concept-portal-excerpt">
              <?php echo esc_html(wp_trim_words(get_post_field('post_content', $portal->ID), 20)); ?>
            </p>
          </div>

        <?php elseif ($topic): ?>
          <?php
            $image_id = function_exists('get_field') ? get_field('theme_cover_image', 'term_' . $topic->term_id) : '';
            if (!$image_id) $image_id = 23557; // fallback image ID
            $image_url = wp_get_attachment_image_url($image_id, 'medium');
          ?>
          <div class="cpt-concept-portal-item">
            <a href="<?php echo esc_url(get_term_link($topic)); ?>" class="cpt-concept-portal-thumb">
              <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($topic->name); ?>">
            </a>
            <a href="<?php echo esc_url(get_term_link($topic)); ?>" class="cpt-concept-portal-title">
              🧩 Topic Page for <?php echo esc_html($concept_title); ?>
            </a>
            <p class="cpt-concept-portal-excerpt">
              <?php echo esc_html($topic->description ?: 'Explore all content tagged under this topic.'); ?>
            </p>
          </div>
        <?php endif; ?>
      </div>
    </section>
  <?php endif; ?>

</div>