<?php
$current_id = get_the_ID();

// 1. Determine current lyric's bucket based on its related Song
$song_field = get_field('song', $current_id);
$current_song_id = 0;
if ($song_field) {
    if (is_array($song_field)) $song_field = reset($song_field);
    $current_song_id = is_object($song_field) ? $song_field->ID : $song_field;
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

// 2. Fetch all lyrics
$all_lyric_ids = get_posts([
    'post_type'      => 'lyric',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'fields'         => 'ids',
]);

// 3. Filter lyrics to match the current bucket
$lyric_ids = [];

foreach ($all_lyric_ids as $lyric_id) {
    $l_song_field = get_field('song', $lyric_id);
    $l_song_id = 0;
    if ($l_song_field) {
        if (is_array($l_song_field)) $l_song_field = reset($l_song_field);
        $l_song_id = is_object($l_song_field) ? $l_song_field->ID : $l_song_field;
    }

    if ($is_rap) {
        // Keep only lyrics attached to Rap songs
        if ($l_song_id && has_term('rap', 'song_category', $l_song_id)) {
            $lyric_ids[] = $lyric_id;
        }
    } else {
        // Keep only lyrics attached to non-Rap songs with the same feature_level
        if ($l_song_id && !has_term('rap', 'song_category', $l_song_id)) {
            $l_terms = wp_get_post_terms($l_song_id, 'feature_level', ['fields' => 'slugs']);
            $l_feature_slug = $l_terms[0] ?? '';
            
            if ($l_feature_slug === $feature_slug) {
                $lyric_ids[] = $lyric_id;
            }
        } elseif (!$l_song_id && empty($feature_slug)) {
            // Fallback for lyrics with no song attached
            $lyric_ids[] = $lyric_id;
        }
    }
}

$current_index = array_search($current_id, $lyric_ids);
$next_id = $lyric_ids[$current_index + 1] ?? null;
$prev_id = $lyric_ids[$current_index - 1] ?? null;

// Helper function to get the image for a lyric's nav
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