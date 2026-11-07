<?php

/* ===== CONFIG ===== */

$excluded_types = ['chapter','fragment','element'];

$all_types = [
 'profile','book','movie','quote','lyric',
 'organization','image','song',
 'excerpt','show','game',
 'rap', 'rapper', 'raplyric'
];

/* Relationship fields */
$relationship_fields = [
 'books_cited',
 'concepts_referenced',
 'excerpts_referenced',
 'lyrics_referenced',
 'movies_referenced',
 'shows_referenced',
 'organizations_referenced',
 'people_referenced',
 'quotes_referenced',
 'images_linked',
 'games_referenced',
];

/* ===== STEP 1: COLLECT REFERENCED IDS ===== */

$referenced_ids = [];

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

/* ===== STEP 2: GET LISTENING ROOM SONGS ===== */
$lr_songs = get_posts([
    'post_type'      => ['song', 'rap'],
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'tax_query'      => [
        [
            'taxonomy' => 'song_group',
            'operator' => 'EXISTS',
        ],
    ],
]);

// Filter out songs that ONLY have "ungrouped"
$ungrouped_term = get_term_by('slug', 'ungrouped', 'song_group');
$lr_song_ids = [];
if (!empty($lr_songs)) {
    foreach ($lr_songs as $sid) {
        $terms = get_the_terms($sid, 'song_group');
        if ($terms && !is_wp_error($terms)) {
            $has_real_group = false;
            foreach ($terms as $t) {
                if (!$ungrouped_term || $t->term_id !== $ungrouped_term->term_id) {
                    $has_real_group = true;
                    break;
                }
            }
            if ($has_real_group) {
                $lr_song_ids[] = $sid;
            }
        }
    }
}

$lr_song_map = array_flip($lr_song_ids);

// DO NOT add LR songs to $referenced_ids — let them flow through as orphans
// but flag them so we can hide/show them with the toggle

// Convert the array keys (IDs) back to a flat array
$referenced_ids = array_keys($referenced_ids);

/* ===== STEP 3: GET ALL POSTS ===== */

$q = new WP_Query([
  'post_type'      => $all_types,
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
  'post_status'    => 'publish'
]);

$entries = [];
$type_counts = [];
$normal_count = 0;
$lr_count = 0;

/* ===== STEP 4: FILTER ===== */

while ($q->have_posts()) {
  $q->the_post();

  $id   = get_the_ID();
  $type = get_post_type();

  if (!in_array($id, $referenced_ids)) {

    $meta  = get_cpt_metadata($type);
    $emoji = ($meta && isset($meta['emoji'])) ? $meta['emoji'] : '•';
    
    $is_lr = isset($lr_song_map[$id]);

    $entries[] = [
      'title' => get_the_title(),
      'url'   => get_permalink(),
      'emoji' => $emoji,
      'type'  => $type,
      'is_lr' => $is_lr
    ];

    if (!isset($type_counts[$type])) {
      $type_counts[$type] = 0;
    }

    $type_counts[$type]++;
    
    if ($is_lr) {
        $lr_count++;
    } else {
        $normal_count++;
    }
  }
}

wp_reset_postdata();

/* ===== SORT ===== */

usort($entries, fn($a,$b)=>strcasecmp($a['title'],$b['title']));
ksort($type_counts);

?>

<section class="tool-orphans">

    <h2>Orphans</h2>

    <p class="tool-orphans-count">
        <?php echo number_format($normal_count); ?> standard orphans
        <?php if ($lr_count > 0): ?>
            &nbsp;|&nbsp; <span class="lr-count-text"><?php echo number_format($lr_count); ?> Listening Room orphans (hidden)</span>
        <?php endif; ?>
    </p>

    <?php if ($lr_count > 0): ?>
    <div class="lr-toggle-wrapper">
        <label>
            <input type="checkbox" id="toggle-lr-orphans">
            Include Listening Room Songs
        </label>
    </div>
    <?php endif; ?>

    <?php get_template_part('template-parts/tools/tool', 'filters', [
      'type_counts' => $type_counts
    ]); ?>

    <ul class="cpt-clean-list">
        <?php foreach ($entries as $e): ?>
            <li data-type="<?php echo esc_attr($e['type']); ?>" <?php if($e['is_lr']) echo 'class="lr-orphan"'; ?>>
                <span class="entry-emoji">
                    <?php echo esc_html($e['emoji']); ?>
                </span>
                <a href="<?php echo esc_url($e['url']); ?>" target="_blank" rel="noopener">
                    <?php echo esc_html($e['title']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('toggle-lr-orphans');
    if (toggle) {
        toggle.addEventListener('change', function() {
            const wrapper = document.querySelector('.tool-orphans');
            const countText = document.querySelector('.lr-count-text');
            if (this.checked) {
                wrapper.classList.add('show-lr');
                if (countText) countText.textContent = countText.textContent.replace('(hidden)', '(shown)');
            } else {
                wrapper.classList.remove('show-lr');
                if (countText) countText.textContent = countText.textContent.replace('(shown)', '(hidden)');
            }
        });
    }
});
</script>