<?php
/**
 * Template part for displaying search results from the "theme" taxonomy
 *
 * Expects:
 * - $info        (array: title, emoji)
 * - $search_term (string)
 */

if (!isset($info) || !isset($search_term)) {
  return;
}

$themes = get_terms([
  'taxonomy'   => 'theme',
  'hide_empty' => false,
  'search'     => $search_term, // broader than name__like
]);

if (!empty($themes) && !is_wp_error($themes)) : ?>
  <section class="search-section search-section-theme">
    <h2><?php echo esc_html($info['emoji'] . ' ' . $info['title']); ?></h2>
    <ul>
      <?php foreach ($themes as $theme) : ?>
        <li>
          <a href="<?php echo esc_url(get_term_link($theme)); ?>">
            <?php echo esc_html($theme->name); ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
<?php endif; ?>
