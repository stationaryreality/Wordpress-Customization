<?php
// Force errors to show
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ===== CONFIG ===== */

$excluded_types = ['chapter','fragment','element'];

$all_types = [
 'profile','book','movie','quote','lyric',
 'organization','image','song',
 'excerpt','show','game',
 'rap', 'rapper', 'raplyric'
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

/* ===== STEP 2: GET SONG GROUP SONGS ===== */
$song_group_songs = get_posts([
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

$song_group_map = array_flip($song_group_songs);

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
$song_group_count = 0;

while ($q->have_posts()) {
  $q->the_post();

  $id   = get_the_ID();
  $type = get_post_type();

  if (!in_array($id, $referenced_ids)) {

    $meta  = get_cpt_metadata($type);
    $emoji = $meta['emoji'] ?? '•';
    
    $is_song_group = (($type === 'song' || $type === 'rap') && isset($song_group_map[$id]));

    $entries[] = [
      'title' => get_the_title(),
      'url'   => get_permalink(),
      'emoji' => $emoji,
      'type'  => $type,
      'is_song_group' => $is_song_group
    ];

    if (!isset($type_counts[$type])) {
      $type_counts[$type] = 0;
    }

    $type_counts[$type]++;
    
    if ($is_song_group) {
        $song_group_count++;
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
        <?php if ($song_group_count > 0): ?>
            &nbsp;|&nbsp; <span class="song-group-count-text"><?php echo number_format($song_group_count); ?> Song Group orphans (hidden)</span>
        <?php endif; ?>
    </p>

    <?php if ($song_group_count > 0): ?>
    <div class="song-group-toggle-wrapper">
        <label>
            <input type="checkbox" id="toggle-song-group-orphans">
            Include Song Group Songs
        </label>
    </div>
    <?php endif; ?>

    <?php get_template_part('template-parts/tools/tool', 'filters', [
      'type_counts' => $type_counts
    ]); ?>

    <ul class="cpt-clean-list">
        <?php foreach ($entries as $e): ?>
            <li data-type="<?php echo esc_attr($e['type']); ?>" <?php if($e['is_song_group']) echo 'class="song-group-orphan"'; ?>>
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
    const toggle = document.getElementById('toggle-song-group-orphans');
    if (toggle) {
        toggle.addEventListener('change', function() {
            const wrapper = document.querySelector('.tool-orphans');
            const countText = document.querySelector('.song-group-count-text');
            if (this.checked) {
                wrapper.classList.add('show-song-group');
                if (countText) countText.textContent = countText.textContent.replace('(hidden)', '(shown)');
            } else {
                wrapper.classList.remove('show-song-group');
                if (countText) countText.textContent = countText.textContent.replace('(shown)', '(hidden)');
            }
        });
    }
});
</script>