<?php
$related = get_field('related_concepts'); // ACF relationship field
?>

<div class="concept-content" style="text-align:center;">

  <div class="concept-definition">
    <?php the_content(); ?>
  </div>

  <?php
$concept_title = get_the_title();
$portal = get_page_by_title($concept_title, OBJECT, 'portal');
$topic  = get_term_by('name', $concept_title, 'topic');

$concept_title = get_the_title();
$portal = get_page_by_title($concept_title, OBJECT, 'portal');

// Only get topic if no portal exists
$topic = $portal ? null : get_term_by('name', $concept_title, 'topic');

if ($portal || $topic): ?>
  <section class="cpt-section concept-portal-topic" style="margin-bottom:4rem; text-align:center;">

    <div class="tag-posts-grid" style="display:flex; justify-content:center; flex-wrap:wrap; gap:2rem;">
      <?php if ($portal): ?>
        <div class="tag-post-item" style="max-width:300px;">
          <a href="<?php echo get_permalink($portal->ID); ?>" class="tag-post-thumbnail">
            <?php if (has_post_thumbnail($portal->ID)): ?>
              <img src="<?php echo esc_url(get_the_post_thumbnail_url($portal->ID, 'medium')); ?>"
                   alt="<?php echo esc_attr(get_the_title($portal->ID)); ?>"
                   style="border-radius:1rem; width:100%; height:auto;">
            <?php endif; ?>
          </a>
          <a href="<?php echo get_permalink($portal->ID); ?>"
             class="tag-post-title"
             style="display:block; font-weight:bold; margin-top:0.5rem;">
            🚪 Portal Page for <?php echo esc_html($concept_title); ?>
          </a>
          <p class="tag-post-excerpt" style="font-size:0.9em; color:#666;">
            <?php echo esc_html(wp_trim_words(get_post_field('post_content', $portal->ID), 20)); ?>
          </p>
        </div>

      <?php elseif ($topic): ?>
        <?php
          // get featured image from term ACF (e.g. theme_cover_image)
          $image_id = function_exists('get_field') ? get_field('theme_cover_image', 'term_' . $topic->term_id) : '';
          if (!$image_id) $image_id = 23557; // fallback image ID if you have one
          $image_url = wp_get_attachment_image_url($image_id, 'medium');
        ?>
        <div class="tag-post-item" style="max-width:300px;">
          <a href="<?php echo esc_url(get_term_link($topic)); ?>" class="tag-post-thumbnail">
            <img src="<?php echo esc_url($image_url); ?>"
                 alt="<?php echo esc_attr($topic->name); ?>"
                 style="border-radius:1rem; width:100%; height:auto;">
          </a>
          <a href="<?php echo esc_url(get_term_link($topic)); ?>"
             class="tag-post-title"
             style="display:block; font-weight:bold; margin-top:0.5rem;">
            🧩 Topic Page for <?php echo esc_html($concept_title); ?>
          </a>
          <p class="tag-post-excerpt" style="font-size:0.9em; color:#666;">
            <?php echo esc_html($topic->description ?: 'Explore all content tagged under this topic.'); ?>
          </p>
        </div>
      <?php endif; ?>
    </div>
  </section>
<?php endif; ?>


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

  <?php get_template_part('content/concept-nav'); ?>
</div>
