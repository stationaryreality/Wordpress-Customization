<?php
/**
 * Organization Display Template
 *
 * Supports two modes:
 *
 * 1. Single Post Mode (original) – uses the current post in the loop
 * 2. Normalized Cards Mode – uses $items array (for lists/grids)
 * 3. WP_Query Mode – loops through a given WP_Query
 */
$query        = $args['query'] ?? null;
$items        = $args['items'] ?? [];
$section_title = $args['title'] ?? '';
$emoji        = $args['emoji'] ?? '';
$search_term  = $args['search_term'] ?? '';

// Early bailout if no data source has content
if (
    empty($items)
    && (!$query instanceof WP_Query || !$query->have_posts())
    && !in_the_loop() // fallback: if we're not in a loop, we might have a single post
) {
    // If we're on a single post, we'll use the post data; otherwise return.
    if (!is_singular('organization')) {
        return;
    }
}

// Determine which data source to use
if (!empty($items)) {
    // Normalized cards mode
    foreach ($items as $item):
        // Extract data
        $org_id   = $item['id'] ?? 0;
        $title    = $item['title'] ?? '';
        $url      = $item['url'] ?? '';
        $image    = $item['image'] ?? ''; // logo
        $bio      = $item['excerpt'] ?? ''; // bio text
        $meta     = $item['meta'] ?? [];
        $wiki_slug = $meta['wiki_slug'] ?? '';
        $people   = $meta['people'] ?? []; // array of ['name' => '', 'url' => '']
        $featured_threads = $meta['featured_threads'] ?? ''; // not used here
        ?>
        <div class="organization-header" style="text-align:center;">
          <?php if ($image): ?>
            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="author-thumbnail" style="border-radius:0; aspect-ratio:1/1; object-fit:cover; max-width:300px; margin-bottom:1em;">
          <?php endif; ?>
          <h1><?php echo esc_html($title); ?></h1>
        </div>

        <div class="organization-bio" style="text-align:center;">
          <?php if ($bio): ?>
            <?php echo wp_kses_post($bio); ?>
          <?php elseif ($wiki_slug): ?>
            <p><?php echo kp_get_wikipedia_intro($wiki_slug); ?></p>
          <?php else: ?>
            <!-- No content available for this mode -->
          <?php endif; ?>
        </div>

        <?php if ($people): ?>
          <div class="related-people" style="margin-top:3em; text-align:center;">
            <h2>Related People</h2>
            <ul style="list-style:none; padding:0; display:inline-block; text-align:left;">
              <?php foreach ($people as $person): ?>
                <li>
                  <a href="<?php echo esc_url($person['url']); ?>">
                    <?php echo esc_html($person['name']); ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php
        // These functions might not exist in items mode; maybe skip or call with parameters
        // show_featured_in_threads('organizations_referenced');
        // get_template_part('template-parts/navigation/organization');
        ?>
    <?php endforeach;
} elseif ($query instanceof WP_Query && $query->have_posts()) {
    // WP_Query mode – loop through posts
    while ($query->have_posts()): $query->the_post();
        $org_id    = get_the_ID();
        $bio       = get_field('org_bio', $org_id);
        $logo      = get_field('cover_image', $org_id);
        $img_url   = $logo ? $logo['sizes']['thumbnail'] : '';
        $wiki_slug = get_field('wikipedia_slug', $org_id);
        $people    = get_field('related_people', $org_id);
        ?>
        <div class="organization-header" style="text-align:center;">
          <?php if ($img_url): ?>
            <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="author-thumbnail" style="border-radius:0; aspect-ratio:1/1; object-fit:cover; max-width:300px; margin-bottom:1em;">
          <?php endif; ?>
          <h1><?php the_title(); ?></h1>
        </div>

        <div class="organization-bio" style="text-align:center;">
          <?php if ($bio): ?>
            <?php echo wp_kses_post($bio); ?>
          <?php elseif ($wiki_slug): ?>
            <p><?php echo kp_get_wikipedia_intro($wiki_slug); ?></p>
          <?php else: ?>
            <?php the_content(); ?>
          <?php endif; ?>
        </div>

        <?php if ($people): ?>
          <div class="related-people" style="margin-top:3em; text-align:center;">
            <h2>Related People</h2>
            <ul style="list-style:none; padding:0; display:inline-block; text-align:left;">
              <?php foreach ($people as $person): ?>
                <li>
                  <a href="<?php echo get_permalink($person->ID); ?>">
                    <?php echo esc_html(get_the_title($person->ID)); ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php show_featured_in_threads('organizations_referenced'); ?>
        <?php get_template_part('template-parts/navigation/organization'); ?>
    <?php endwhile;
    wp_reset_postdata();
} else {
    // Fallback: single post mode – original behavior (assumes we're in the loop)
    $org_id    = get_the_ID();
    $bio       = get_field('org_bio', $org_id);
    $logo      = get_field('cover_image', $org_id);
    $img_url   = $logo ? $logo['sizes']['thumbnail'] : '';
    $wiki_slug = get_field('wikipedia_slug', $org_id);
    $people    = get_field('related_people', $org_id);
    ?>
    <div class="organization-header" style="text-align:center;">
      <?php if ($img_url): ?>
        <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="author-thumbnail" style="border-radius:0; aspect-ratio:1/1; object-fit:cover; max-width:300px; margin-bottom:1em;">
      <?php endif; ?>
      <h1><?php the_title(); ?></h1>
    </div>

    <div class="organization-bio" style="text-align:center;">
      <?php if ($bio): ?>
        <?php echo wp_kses_post($bio); ?>
      <?php elseif ($wiki_slug): ?>
        <p><?php echo kp_get_wikipedia_intro($wiki_slug); ?></p>
      <?php else: ?>
        <?php the_content(); ?>
      <?php endif; ?>
    </div>

    <?php if ($people): ?>
      <div class="related-people" style="margin-top:3em; text-align:center;">
        <h2>Related People</h2>
        <ul style="list-style:none; padding:0; display:inline-block; text-align:left;">
          <?php foreach ($people as $person): ?>
            <li>
              <a href="<?php echo get_permalink($person->ID); ?>">
                <?php echo esc_html(get_the_title($person->ID)); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php show_featured_in_threads('organizations_referenced'); ?>
    <?php get_template_part('template-parts/navigation/organization'); ?>
<?php } ?>