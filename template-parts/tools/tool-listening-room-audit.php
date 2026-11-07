<?php
/* ===== LISTENING ROOM AUDIT TOOL ===== */

/* ===== STEP 1: COLLECT REFERENCED IDS ===== */
$referenced_ids = [];

$excluded_types = ['chapter','fragment','element'];

$containers = new WP_Query([
    'post_type'      => $excluded_types,
    'posts_per_page' => -1,
    'post_status'    => 'publish',
]);

while ($containers->have_posts()) {
    $containers->the_post();

    $context = kp_build_reference_context(get_the_ID());

    foreach ($context as $items) {
        foreach ($items as $item) {
            if ($item instanceof WP_Post) {
                $referenced_ids[$item->ID] = true;
            }
        }
    }

    $songs = get_field('chapter_songs', get_the_ID());

    if ($songs) {
        foreach ($songs as $row) {
            if (!empty($row['song']) && $row['song'] instanceof WP_Post) {
                $referenced_ids[$row['song']->ID] = true;
            }
        }
    }
}

wp_reset_postdata();

/* ===== STEP 2: GET ALL LISTENING ROOM SONGS ===== */
$lr_posts = get_posts([
    'post_type'      => ['song', 'rap'],
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'tax_query'      => [
        [
            'taxonomy' => 'song_group',
            'operator' => 'EXISTS',
        ],
    ],
]);

/* ===== STEP 3: ORGANIZE BY GROUP ===== */
$groups_data = [];
$orphan_count = 0;
$total_count = 0;

foreach ($lr_posts as $post) {
    $terms = get_the_terms($post->ID, 'song_group');
    
    if (!$terms || is_wp_error($terms)) continue;
    
    // Skip songs that only have "ungrouped"
    $has_real_group = false;
    $group_name = 'Ungrouped';
    
    foreach ($terms as $term) {
        if ($term->slug !== 'ungrouped') {
            $has_real_group = true;
            $group_name = $term->name;
            break;
        }
    }
    
    if (!$has_real_group) continue;
    
    $is_orphan = !isset($referenced_ids[$post->ID]);
    
    if ($is_orphan) $orphan_count++;
    $total_count++;
    
    if (!isset($groups_data[$group_name])) {
        $groups_data[$group_name] = [];
    }
    
    $meta  = get_cpt_metadata(get_post_type($post->ID));
    $emoji = ($meta && isset($meta['emoji'])) ? $meta['emoji'] : '•';
    
    $groups_data[$group_name][] = [
        'title'     => get_the_title($post->ID),
        'url'       => get_permalink($post->ID),
        'emoji'     => $emoji,
        'type'      => get_post_type($post->ID),
        'is_orphan' => $is_orphan,
    ];
}

// Sort groups alphabetically
ksort($groups_data);

// Sort songs within each group alphabetically
foreach ($groups_data as &$songs) {
    usort($songs, fn($a, $b) => strcasecmp($a['title'], $b['title']));
}
unset($songs);
?>

<section class="tool-lr-audit">

    <h2>Listening Room Audit</h2>

    <p class="tool-lr-audit-count">
        <?php echo number_format($total_count); ?> songs in Listening Room
        &nbsp;|&nbsp;
        <span class="lr-orphan-count"><?php echo number_format($orphan_count); ?> orphans</span>
    </p>

    <div class="lr-audit-toggle-wrapper">
        <label>
            <input type="checkbox" id="toggle-lr-orphans-only">
            Show only orphans (songs not referenced anywhere else)
        </label>
    </div>

    <?php foreach ($groups_data as $group_name => $songs): ?>
        <div class="lr-audit-group">
            <h3 class="lr-audit-group-title"><?php echo esc_html($group_name); ?></h3>
            <ul class="lr-audit-list">
                <?php foreach ($songs as $song): ?>
                    <li class="<?php if($song['is_orphan']) echo 'lr-audit-orphan'; ?>"
                        data-orphan="<?php echo $song['is_orphan'] ? '1' : '0'; ?>">
                        <span class="lr-audit-emoji"><?php echo esc_html($song['emoji']); ?></span>
                        <a href="<?php echo esc_url($song['url']); ?>" target="_blank" rel="noopener">
                            <?php echo esc_html($song['title']); ?>
                        </a>
                        <?php if ($song['is_orphan']): ?>
                            <span class="lr-audit-orphan-badge">orphan</span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>

</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('toggle-lr-orphans-only');
    const wrapper = document.querySelector('.tool-lr-audit');
    
    if (toggle) {
        toggle.addEventListener('change', function() {
            if (this.checked) {
                wrapper.classList.add('show-orphans-only');
            } else {
                wrapper.classList.remove('show-orphans-only');
            }
        });
    }
});
</script>