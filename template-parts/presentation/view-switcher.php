<?php

$view = $args['view'] ?? 'index';

$allowed_views = [
    'index',
    'list',
    'atlas',
];

?>

<div class="portal-view-switcher">

<label>

View:

<select onchange="window.location=this.value">

<?php foreach ($allowed_views as $allowed) : ?>

<option
    value="?view=<?php echo esc_attr($allowed); ?>"
    <?php selected($view, $allowed); ?>
>

<?php echo ucfirst($allowed); ?>

</option>

<?php endforeach; ?>

</select>

</label>

</div>