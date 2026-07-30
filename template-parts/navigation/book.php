<?php
$current_id = get_the_ID();
$book_ids = get_posts([
  'post_type' => 'book',
  'posts_per_page' => -1,
  'orderby' => 'title',
  'order' => 'ASC',
  'fields' => 'ids',
]);

$current_index = array_search($current_id, $book_ids);
$next_id = $book_ids[$current_index + 1] ?? null;
$prev_id = $book_ids[$current_index - 1] ?? null;
?>

<div class="cpt-book-nav">
  <?php if ($next_id): ?>
    <div class="cpt-book-nav-next">
      <h2>Next Book</h2>
      <a href="<?php echo get_permalink($next_id); ?>">
        <?php
        $cover = get_field('cover_image', $next_id);
        if ($cover) {
          echo '<img src="' . esc_url($cover['sizes']['medium']) . '" alt="' . esc_attr(get_the_title($next_id)) . '" class="cpt-book-nav-img">';
        }
        ?>
        <h3><?php echo get_the_title($next_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>

  <?php if ($prev_id): ?>
    <div class="cpt-book-nav-prev">
      <h2>Previous Book</h2>
      <a href="<?php echo get_permalink($prev_id); ?>">
        <?php
        $cover = get_field('cover_image', $prev_id);
        if ($cover) {
          echo '<img src="' . esc_url($cover['sizes']['medium']) . '" alt="' . esc_attr(get_the_title($prev_id)) . '" class="cpt-book-nav-img">';
        }
        ?>
        <h3><?php echo get_the_title($prev_id); ?></h3>
      </a>
    </div>
  <?php endif; ?>
</div>