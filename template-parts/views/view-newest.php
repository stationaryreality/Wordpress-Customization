<?php

$map = get_cpt_metadata();

$post_types = [
    'artist','profile','book','concept','movie','quote','lyric',
    'organization','image','song','chapter',
    'excerpt','fragment','element','show','game', 'portal', 'video'
];

$icons = [];
foreach ($post_types as $pt) {
    $icons[$pt] = $map[$pt]['emoji'] ?? '❓';
}

/* ===== QUERY POSTS (Capped to last 6 months, max 500) ===== */

$six_months_ago = date('Y-m-d', strtotime('-6 months'));

$q = new WP_Query([
    'post_type'      => $post_types,
    'posts_per_page' => 500,  // Hard cap
    'orderby'        => 'date',
    'order'          => 'DESC',
    'post_status'    => 'publish',
    'date_query'     => [
        [
            'after' => $six_months_ago,
        ]
    ]
]);

$tree = [];
$total_count = 0;

while ($q->have_posts()) {
    $q->the_post();

    $t = get_post_time('U');
    $y = date('Y', $t);
    $m = date('n', $t);
    $d = date('j', $t);

    $tree[$y][$m][$d][] = [
        'title' => get_the_title(),
        'url'   => get_permalink(),
        'time'  => date('H:i', $t),
        'icon'  => $icons[get_post_type()]
    ];

    $total_count++;
}

wp_reset_postdata();

/* ===== FORCE ORDER (Newest first) ===== */

krsort($tree);
foreach ($tree as &$months) {
    krsort($months);
    foreach ($months as &$days) {
        krsort($days);
    }
}

?>

<section class="tool-newest-content">

    <header class="tool-header">
        <h2>Newest Content</h2>
        <p class="cpt-total">
            <?php echo number_format($total_count); ?> entries in the last 6 months
        </p>
        <p style="margin: 1rem 0; font-size: 0.9rem; color: #666;">
            Showing content published since <?php echo date('F j, Y', strtotime('-6 months')); ?>.
            <a href="?tool=index">View full site index →</a>
        </p>
    </header>

    <?php if ($total_count === 0): ?>
        <p>No new content in the last 6 months.</p>
    <?php else: ?>

        <?php foreach ($tree as $year => $months): ?>
            <h2><?php echo $year; ?></h2>

            <?php foreach ($months as $month => $days): ?>
                <h3><?php echo date('F', mktime(0, 0, 0, $month, 1)); ?></h3>

                <?php foreach ($days as $day => $items): ?>
                    <h4><?php echo $day; ?></h4>
                    <ul>
                        <?php foreach ($items as $item): ?>
                            <li>
                                <span class="cpt-icon"><?php echo $item['icon']; ?></span>
                                <a href="<?php echo $item['url']; ?>">
                                    <?php echo $item['title']; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>

    <?php endif; ?>

</section>