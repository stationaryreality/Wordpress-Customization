<?php
// Alphabetical navigation for Book CPT
$current_id = get_the_ID();
$book_ids = get_posts([
  'post_type' => 'book',
  'posts_per_page' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
  'fields' => 'ids',
]);

$current_index = array_search($current_id, $book_ids);
$next_id = $book_ids[$current_index + 1] ?? null; // NEXT in alphabet
$prev_id = $book_ids[$current_index - 1] ?? null; // PREVIOUS in alphabet
?>

<div class="post-navigation-container books-nav" style="display: flex; justify-content: center; gap: 60px; margin-top: 60px;">
<?php if ($next_id): ?>
  <div class="previous-post" style="text-align: center;">
    <h2>Next Book</h2>
    <a href="<?php echo get_permalink($next_id); ?>" style="display:inline-block;">
      <?php
      $cover = get_field('cover_image', $next_id);
      if ($cover) {
        echo '<img src="' . esc_url($cover['sizes']['medium']) . '" alt="' . esc_attr(get_the_title($next_id)) . '" style="width:150px;height:auto;margin-bottom:10px;">';
      }
      ?>
      <h3><?php echo get_the_title($next_id); ?></h3>
    </a>
  </div>
<?php endif; ?>


<?php if ($prev_id): ?>
  <div class="next-post" style="text-align: center;">
    <h2>Previous Book</h2>
    <a href="<?php echo get_permalink($prev_id); ?>" style="display:inline-block;">
      <?php
      $cover = get_field('cover_image', $prev_id);
      if ($cover) {
        echo '<img src="' . esc_url($cover['sizes']['medium']) . '" alt="' . esc_attr(get_the_title($next_id)) . '" style="width:150px;height:auto;margin-bottom:10px;">';
      }
      ?>
      <h3><?php echo get_the_title($prev_id); ?></h3>
    </a>
  </div>
<?php endif; ?>

</div>
