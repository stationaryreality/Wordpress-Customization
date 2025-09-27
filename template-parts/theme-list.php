<?php
/**
 * Shared Theme List Template
 *
 * Expects:
 * - terms       => array of WP_Term objects
 * - title       => Section/page title
 * - emoji       => Emoji (optional)
 * - search_term => Optional (only used on search)
 */
$terms       = $args['terms'] ?? [];
$title       = $args['title'] ?? 'Themes';
$emoji       = $args['emoji'] ?? '';
$search_term = $args['search_term'] ?? '';

if (empty($terms)) return;

// Dummy fallback image ID (replace with real one later)
$dummy_image = wp_get_attachment_image_url(19327, 'thumbnail');
?>

<section class="theme-grid container" style="max-width: 800px; margin: auto; padding: 2rem 1rem;">
  <h1>
    <?php if ($emoji) echo $emoji . ' '; ?>
    <?php echo esc_html($title); ?>
    <?php if ($search_term) : ?>
      containing “<?php echo esc_html($search_term); ?>”
    <?php endif; ?>
  </h1>
  <p class="intro-text">Collected themes that connect songs, narratives, and other works.</p>

  <div class="theme-list">
    <?php foreach ($terms as $theme): ?>
      <?php 
        $term_link = get_term_link($theme);
        if (is_wp_error($term_link)) continue;
      ?>
      <div class="theme-entry" style="display:flex; align-items:flex-start; gap:1rem; margin-bottom:2rem; border-bottom:1px solid #ddd; padding-bottom:1rem;">
        <a href="<?php echo esc_url($term_link); ?>" class="theme-thumb">
          <img src="<?php echo esc_url($dummy_image); ?>" alt="Theme thumbnail" style="width:48px; height:48px; border-radius:50%; object-fit:cover;">
        </a>

        <div class="theme-text">
          <h2 style="margin-bottom:0.5rem;">
            <a href="<?php echo esc_url($term_link); ?>">
              <?php echo esc_html($theme->name); ?>
            </a>
          </h2>

          <?php if ($theme->description): ?>
            <p style="margin:0;"><?php echo esc_html(wp_trim_words(strip_tags($theme->description), 30, '...')); ?></p>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
