<?php
$current_id = get_the_ID();

// 1. Determine current lyric's bucket based on its related Song
$song_field = get_field('song', $current_id);
$current_song_id = 0;
if ($song_field) {
    if (is_array($song_field)) $song_field = reset($song_field);
    $current_song_id = is_object($song_field) ? $song_field->ID : (int)$song_field;
}

$is_rap = false;
$feature_slug = '';
$heading_prefix = 'Lyric';

if ($current_song_id) {
    if (has_term('rap', 'song_category', $current_song_id)) {
        $is_rap = true;
        $heading_prefix = 'Rap Lyric';
    } else {
        $terms = wp_get_post_terms($current_song_id, 'feature_level', ['fields' => 'slugs']);
        $feature_slug = $terms[0] ?? '';
        
        $heading_prefix = match($feature_slug) {
            'narrative'  => 'Narrative Lyric',
            'featured'   => 'Featured Lyric',
            'referenced' => 'Referenced Lyric',
            default      => 'Lyric'
        };
    }
}

// 2. Get all target Song IDs for this bucket (1 DB Query)
$song_args = [
    'post_type'      => 'song',
    'posts_per_page' => -1,
    'fields'         => 'ids',
];

if ($is_rap) {
    $song_args['tax_query'] = [['taxonomy' => 'song_category', 'field' => 'slug', 'terms' => 'rap']];
} else {
    $song_args['tax_query'] = [
        'relation' => 'AND',
        ['taxonomy' => 'feature_level', 'field' => 'slug', 'terms' => $feature_slug],
        ['taxonomy' => 'song_category', 'field' => 'slug', 'terms' => 'rap', 'operator' => 'NOT IN']
    ];
}

$target_song_ids = get_posts($song_args);
$target_song_map = array_flip($target_song_ids); // Creates a lightning-fast hash map for lookups

// 3. Get all Lyric IDs ordered by title (1 DB Query)
$all_lyric_ids = get_posts([
    'post_type'      => 'lyric',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'fields'         => 'ids',
]);

// 4. Prime the meta cache for all lyrics at once (1 DB Query)
// This is the magic step: it fetches the ACF 'song' field for EVERY lyric in one go.
if (!empty($all_lyric_ids)) {
    update_meta_cache('post', $all_lyric_ids);
}

// 5. Filter lyrics in PHP memory (0 DB Queries)
$lyric_ids = [];
foreach ($all_lyric_ids as $lyric_id) {
    // Because of update_meta_cache, this reads from RAM, not the database
    $song_meta_values = get_post_meta($lyric_id, 'song', false);
    
    $match_found = false;
    foreach ($song_meta_values as $meta_val) {
        // Handle standard numeric ID
        if (is_numeric($meta_val) && isset($target_song_map[(int)$meta_val])) {
            $match_found = true;
            break;
        }
        // Handle ACF serialized arrays (Relationship fields store data serialized)
        if (is_string($meta_val) && is_serialized($meta_val)) {
            $unserialized = maybe_unserialize($meta_val);
            if (is_array($unserialized)) {
                foreach ($unserialized as $sub_val) {
                    $sub_id = is_object($sub_val) ? $sub_val->ID : (int)$sub_val;
                    if (isset($target_song_map[$sub_id])) {
                        $match_found = true;
                        break;
                    }
                }
            }
        }
    }
    
    if ($match_found) {
        $lyric_ids[] = $lyric_id;
    }
}

$current_index = array_search($current_id, $lyric_ids);
$next_id = $lyric_ids[$current_index + 1] ?? null;
$prev_id = $lyric_ids[$current_index - 1] ?? null;

// Helper function to get the image for a lyric's nav
// This only runs a maximum of 2 times now (once for prev, once for next)
function get_lyric_nav_image($lyric_id) {
    $song = get_field('song', $lyric_id);
    if ($song) {
        if (is_array($song)) $song = reset($song);
        $song_id = is_object($song) ? $song->ID : $song;
        
        $cover = get_field('cover_image', $song_id);
        if ($cover) {
            if (is_array($cover)) return $cover['sizes']['thumbnail'] ?? $cover['url'] ?? '';
            if (is_numeric($cover)) return wp_get_attachment_image_url($cover, 'thumbnail');
            return $cover;
        }
        if (has_post_thumbnail($song_id)) return get_the_post_thumbnail_url($song_id, 'thumbnail');
    }
    if (has_post_thumbnail($lyric_id)) return get_the_post_thumbnail_url($lyric_id, 'thumbnail');
    return '';
}

$prev_image = $prev_id ? get_lyric_nav_image($prev_id) : '';
$next_image = $next_id ? get_lyric_nav_image($next_id) : '';
?>

<div class="cpt-lyric-nav-top">
    <div class="cpt-lyric-nav-row">
        <?php if ($prev_id): ?>
            <a href="<?php echo get_permalink($prev_id); ?>" class="cpt-lyric-nav-prev cpt-keyboard-nav-prev">
                <span class="cpt-lyric-nav-label">← Previous <?php echo esc_html($heading_prefix); ?></span>
                <?php if ($prev_image): ?>
                    <img src="<?php echo esc_url($prev_image); ?>" alt="" class="cpt-lyric-nav-thumb">
                <?php endif; ?>
                <span class="cpt-lyric-nav-title"><?php echo get_the_title($prev_id); ?></span>
            </a>
        <?php endif; ?>

        <?php if ($prev_id || $next_id): ?>
            <span class="cpt-keyboard-hint-inline" title="Use arrow keys to navigate">
                Use ← ⌨️ → keys
            </span>
        <?php endif; ?>

        <?php if ($next_id): ?>
            <a href="<?php echo get_permalink($next_id); ?>" class="cpt-lyric-nav-next cpt-keyboard-nav-next">
                <span class="cpt-lyric-nav-label">Next <?php echo esc_html($heading_prefix); ?> →</span>
                <?php if ($next_image): ?>
                    <img src="<?php echo esc_url($next_image); ?>" alt="" class="cpt-lyric-nav-thumb">
                <?php endif; ?>
                <span class="cpt-lyric-nav-title"><?php echo get_the_title($next_id); ?></span>
            </a>
        <?php endif; ?>
    </div>
</div>