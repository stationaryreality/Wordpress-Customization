<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

if ($containers->have_posts()) {
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
}

wp_reset_postdata();

/* ===== STEP 2: GET SONG GROUP POSTS (For Toggle) ===== */
// Songs with song_group taxonomy assigned (not the "ungrouped" default)
$grouped_songs = get_posts([
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

// Exclude songs that only have the "ungrouped" term
$ungrouped_term = get_term_by('slug', 'ungrouped', 'song_group');
if ($ungrouped_term) {
    $ungrouped_only = get_posts([
        'post_type'      => ['song', 'rap'],
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => [
            [
                'taxonomy' => 'song_group',
                'field'    => 'term_id',
                'terms'    => [$ungrouped_term->term_id],
                'operator' => 'IN',
            ],
            [
                'taxonomy' => 'song_group',
                'operator' => 'COUNT',
                'terms'    => 1,
            ],
        ],
    ]);
    $grouped_songs = array_diff($grouped_songs, $ungrouped_only);
}

$grouped_songs_map = !empty($grouped_songs) ? array_flip($grouped_songs) : [];

/* ===== STEP 3: GET ALL POSTS & FILTER ===== */

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
$grouped_count = 0;

if ($q->have_posts()) {
    while ($q->have_posts()) {
      $q->the_post();

      $id   = get_the_ID();
      $type = get_post_type();

      if (!in_array($id, $referenced_ids)) {

        $meta  = get_cpt_metadata($type);
        $emoji = $meta['emoji'] ?? '•';
        
        $is_grouped = (($type === 'song' || $type === 'rap') && isset($grouped_songs_map[$id]));

        $entries[] = [
          'title' => get_the_title(),
          'url'   => get_permalink(),
          'emoji' => $emoji,
          'type'  => $type,
          'is_grouped' => $is_grouped
        ];

        if (!isset($type_counts[$type])) {
          $type_counts[$type] = 0;
        }

        $type_counts[$type]++;
        
        if ($is_grouped) {
            $grouped_count++;
        } else {
            $normal_count++;
        }
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
        <?php if ($grouped_count > 0): ?>
            &nbsp;|&nbsp; <span class="grouped-count-text"><?php echo number_format($grouped_count); ?> Song Group orphans (hidden)</span>
        <?php endif; ?>
    </p>

    <?php if ($grouped_count > 0): ?>
    <div class="grouped-toggle-wrapper">
        <label>
            <input type="checkbox" id="toggle-grouped-orphans">
            Include Song Group Songs
        </label>
    </div>
    <?php endif; ?>

    <?php get_template_part('template-parts/tools/tool', 'filters', [
      'type_counts' => $type_counts
    ]); ?>

    <ul class="cpt-clean-list">
        <?php foreach ($entries as $e): ?>
            <li data-type="<?php echo esc_attr($e['type']); ?>" <?php if($e['is_grouped']) echo 'class="grouped-orphan"'; ?>>
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
    const toggle = document.getElementById('toggle-grouped-orphans');
    if (toggle) {
        toggle.addEventListener('change', function() {
            const wrapper = document.querySelector('.tool-orphans');
            const countText = document.querySelector('.grouped-count-text');
            if (this.checked) {
                wrapper.classList.add('show-grouped');
                if (countText) countText.textContent = countText.textContent.replace('(hidden)', '(shown)');
            } else {
                wrapper.classList.remove('show-grouped');
                if (countText) countText.textContent = countText.textContent.replace('(shown)', '(hidden)');
            }
        });
    }
});
</script>