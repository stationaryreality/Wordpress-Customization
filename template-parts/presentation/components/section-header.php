<?php

$title = $args['title'] ?? '';

$emoji = $args['emoji'] ?? '';

$count = $args['count'] ?? null;

?>

<header class="knowledge-section-header">

    <h2>

        <?php echo esc_html($emoji); ?>

        <?php echo esc_html($title); ?>

        <?php if ($count !== null) : ?>

            <small>

                (<?php echo intval($count); ?>)

            </small>

        <?php endif; ?>

    </h2>

</header>