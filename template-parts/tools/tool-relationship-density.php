<?php
/*
|--------------------------------------------------------------------------
| Topic ↔ Theme Relationship Density Analyzer
|--------------------------------------------------------------------------
|
| Analyzes co-occurrence density between topic and theme taxonomies.
| Reveals semantic clusters and recurring conceptual relationships.
|
*/

$all_posts = get_posts([
    'post_type'      => 'any',
    'posts_per_page' => -1,
    'post_status'    => 'publish'
]);

$relationship_map = [];
$topic_totals = [];
$theme_totals = [];

foreach ($all_posts as $post) {

    $topics = wp_get_post_terms($post->ID, 'topic');
    $themes = wp_get_post_terms($post->ID, 'theme');

    if (empty($topics) || empty($themes)) {
        continue;
    }

    // Count total appearances
    foreach ($topics as $topic) {
        if (!isset($topic_totals[$topic->term_id])) {
            $topic_totals[$topic->term_id] = [
                'name'  => $topic->name,
                'count' => 0
            ];
        }

        $topic_totals[$topic->term_id]['count']++;
    }

    foreach ($themes as $theme) {
        if (!isset($theme_totals[$theme->term_id])) {
            $theme_totals[$theme->term_id] = [
                'name'  => $theme->name,
                'count' => 0
            ];
        }

        $theme_totals[$theme->term_id]['count']++;
    }

    // Build topic ↔ theme relationships
    foreach ($topics as $topic) {

        foreach ($themes as $theme) {

            $key = $topic->term_id . '_' . $theme->term_id;

            if (!isset($relationship_map[$key])) {

                $relationship_map[$key] = [
                    'topic' => $topic->name,
                    'theme' => $theme->name,
                    'count' => 0,
                    'posts' => []
                ];
            }

            $relationship_map[$key]['count']++;

            $relationship_map[$key]['posts'][] = [
                'id'    => $post->ID,
                'title' => get_the_title($post->ID)
            ];
        }
    }
}

// Sort strongest relationships first
usort($relationship_map, function($a, $b) {
    return $b['count'] - $a['count'];
});

?>

<section class="semantic-density-tool">

<style>

.semantic-density-tool {
    font-family: sans-serif;
    max-width: 1400px;
    margin: 0 auto;
}

.semantic-table {
    width: 100%;
    border-collapse: collapse;
}

.semantic-table th,
.semantic-table td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

.semantic-table th {
    background: #f5f5f5;
}

.density-high {
    color: #155724;
    font-weight: bold;
}

.density-medium {
    color: #856404;
}

.density-low {
    color: #721c24;
}

.semantic-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    background: #efefef;
    font-size: 12px;
}

</style>

<h1>Semantic Relationship Density Analyzer</h1>

<p>
This analyzer reveals recurring relationships between Topics and Themes
across the site.
</p>

<table class="semantic-table">

<thead>
<tr>
    <th>Rank</th>
    <th>Topic</th>
    <th>Theme</th>
    <th>Relationship Density</th>
    <th>Strength</th>
</tr>
</thead>

<tbody>

<?php foreach ($relationship_map as $index => $relation):

    $strength = 'Low';

    if ($relation['count'] >= 15) {
        $strength = 'High';
        $class = 'density-high';
    }
    elseif ($relation['count'] >= 7) {
        $strength = 'Medium';
        $class = 'density-medium';
    }
    else {
        $class = 'density-low';
    }

?>

<tr>

<td><?php echo $index + 1; ?></td>

<td>
    <span class="semantic-badge">
        <?php echo esc_html($relation['topic']); ?>
    </span>
</td>

<td>
    <span class="semantic-badge">
        <?php echo esc_html($relation['theme']); ?>
    </span>
</td>

<td>
    <?php echo $relation['count']; ?> shared posts
</td>

<td class="<?php echo $class; ?>">
    <?php echo $strength; ?>
</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</section>