<?php

$items = $args['items'] ?? [];
$query = $args['query'] ?? null;

$title = $args['title'] ?? 'Video Games';
$emoji = $args['emoji'] ?? '';

if ($query) {

    $items = [];

    while ($query->have_posts()) {

        $query->the_post();

        $items[] = kp_build_card(
            'game',
            get_the_ID(),
            get_cpt_metadata()
        );

    }

    wp_reset_postdata();

}

if (empty($items)) {
    return;
}

?>

<section class="cpt-section game-grid">

<h2>

<?php if ($emoji): ?>

<?= esc_html($emoji) ?>

<?php endif; ?>

<?= esc_html($title) ?>

</h2>

<div class="tag-posts-grid">

<?php foreach ($items as $item): ?>

<div class="tag-post-item">

<?php if (!empty($item['image'])): ?>

<a href="<?= esc_url($item['url']) ?>">

<img
src="<?= esc_url($item['image']) ?>"
alt="<?= esc_attr($item['title']) ?>">

</a>

<?php endif; ?>

<a
class="tag-post-title"
href="<?= esc_url($item['url']) ?>">

<?= esc_html($item['title']) ?>

</a>

<?php if (!empty($item['excerpt'])): ?>

<p>

<?= esc_html($item['excerpt']) ?>

</p>

<?php endif; ?>

</div>

<?php endforeach; ?>

</div>

</section>