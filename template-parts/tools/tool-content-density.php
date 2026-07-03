<?php
/*
|--------------------------------------------------------------------------
| Hierarchical CPT Density & Structural Pressure Analyzer
|--------------------------------------------------------------------------
|
| Analyzes structural density across:
| Chapters → Fragments → Elements
|
| Outputs:
| - Fragment demotion candidates (too small / underused)
| - Fragment promotion candidates (too dense / overloaded)
| - Element promotion candidates (too dense, should become fragment)
|
*/

$all_posts = get_posts([
    'post_type'      => ['chapter', 'fragment', 'element'],
    'posts_per_page' => -1,
    'post_status'    => 'publish'
]);

/*
|--------------------------------------------------------------------------
| CONFIG (tweak freely)
|--------------------------------------------------------------------------
*/
$CONFIG = [
    'element_promotion_threshold'  => 8,   // element → fragment
    'fragment_demotion_threshold'  => 15,  // fragment → element
    'fragment_promotion_threshold' => 25,  // fragment → chapter (optional future rule)
];

/*
|--------------------------------------------------------------------------
| STORAGE
|--------------------------------------------------------------------------
*/
$cf_map = []; // chapter → fragment
$fe_map = []; // fragment → element

$chapter_stats  = [];
$fragment_stats = [];
$element_stats  = [];

/*
|--------------------------------------------------------------------------
| BUILD STRUCTURE MAPS
|--------------------------------------------------------------------------
*/
foreach ($all_posts as $post) {

    $type = get_post_type($post->ID);

    /*
    |--------------------------------------------------------------------------
    | ELEMENT LOGIC
    |--------------------------------------------------------------------------
    */
    if ($type === 'element') {

        $element_stats[$post->ID]['title'] = get_the_title($post->ID);
        $element_stats[$post->ID]['count'] = ($element_stats[$post->ID]['count'] ?? 0) + 1;

        // find parent fragments (ACF or relationship field assumed)
        $parent_fragments = get_field('parent_fragments', $post->ID) ?: [];

        foreach ($parent_fragments as $fragment_id) {

            $fe_map[$fragment_id][$post->ID] = ($fe_map[$fragment_id][$post->ID] ?? 0) + 1;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FRAGMENT LOGIC
    |--------------------------------------------------------------------------
    */
    if ($type === 'fragment') {

        $fragment_stats[$post->ID]['title'] = get_the_title($post->ID);
        $fragment_stats[$post->ID]['element_count'] = 0;

        $parent_chapters = get_field('parent_chapters', $post->ID) ?: [];

        foreach ($parent_chapters as $chapter_id) {

            $cf_map[$chapter_id][$post->ID] = ($cf_map[$chapter_id][$post->ID] ?? 0) + 1;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CHAPTER LOGIC
    |--------------------------------------------------------------------------
    */
    if ($type === 'chapter') {

        $chapter_stats[$post->ID]['title'] = get_the_title($post->ID);
        $chapter_stats[$post->ID]['fragment_count'] = 0;
    }
}

/*
|--------------------------------------------------------------------------
| ANALYSIS PASS
|--------------------------------------------------------------------------
*/
$insights = [];

/*
|--------------------------------------------------------------------------
| ELEMENT → FRAGMENT PROMOTION CHECK
|--------------------------------------------------------------------------
*/
foreach ($fe_map as $fragment_id => $elements) {

    $element_count = array_sum($elements);

    $fragment_title = $fragment_stats[$fragment_id]['title'] ?? 'Unknown Fragment';

    if ($element_count >= $CONFIG['element_promotion_threshold']) {

        $insights[] = [
            'type'    => 'ELEMENT_PROMOTE',
            'parent'  => 'Fragment',
            'name'    => $fragment_title,
            'score'   => $element_count,
            'message' => 'Element density is high — candidate for promotion into Fragment'
        ];
    }
}

/*
|--------------------------------------------------------------------------
| FRAGMENT → ELEMENT DEMOTION CHECK
|--------------------------------------------------------------------------
*/
foreach ($fe_map as $fragment_id => $elements) {

    $element_count = array_sum($elements);

    $fragment_title = $fragment_stats[$fragment_id]['title'] ?? 'Unknown Fragment';

    if ($element_count <= $CONFIG['fragment_demotion_threshold']) {

        $insights[] = [
            'type'    => 'FRAGMENT_DEMOTE',
            'parent'  => 'Fragment',
            'name'    => $fragment_title,
            'score'   => $element_count,
            'message' => 'Fragment is underutilized — candidate for collapse into Elements'
        ];
    }
}

/*
|--------------------------------------------------------------------------
| CHAPTER → FRAGMENT OVERLOAD CHECK
|--------------------------------------------------------------------------
*/
foreach ($cf_map as $chapter_id => $fragments) {

    $fragment_count = array_sum($fragments);

    $chapter_title = $chapter_stats[$chapter_id]['title'] ?? 'Unknown Chapter';

    if ($fragment_count > 30) {

        $insights[] = [
            'type'    => 'CHAPTER_OVERLOAD',
            'parent'  => 'Chapter',
            'name'    => $chapter_title,
            'score'   => $fragment_count,
            'message' => 'Chapter is highly fragmented — consider breaking into multiple Chapters'
        ];
    }
}

/*
|--------------------------------------------------------------------------
| SORT INSIGHTS BY SEVERITY
|--------------------------------------------------------------------------
*/
usort($insights, function($a, $b) {
    return $b['score'] <=> $a['score'];
});
?>

<section class="hierarchical-density-tool">

<style>

.hierarchical-density-tool {
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

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    background: #efefef;
    font-size: 12px;
}

.insight-box {
    padding: 6px 10px;
    border-radius: 4px;
    background: #f8f8f8;
}

</style>

<h1>Hierarchical CPT Density Analyzer</h1>

<p>
Analyzes structural pressure across Chapters, Fragments, and Elements.
Detects when content is too dense or too thin for its current layer.
</p>

<table class="semantic-table">

<thead>
<tr>
    <th>Rank</th>
    <th>Type</th>
    <th>Name</th>
    <th>Density Score</th>
    <th>Signal</th>
</tr>
</thead>

<tbody>

<?php foreach ($insights as $i => $row):

    $class = 'density-low';

    if ($row['score'] >= 25) {
        $class = 'density-high';
    }
    elseif ($row['score'] >= 15) {
        $class = 'density-medium';
    }

?>

<tr>

<td><?php echo $i + 1; ?></td>

<td>
    <span class="badge"><?php echo esc_html($row['type']); ?></span>
</td>

<td>
    <?php echo esc_html($row['name']); ?>
</td>

<td>
    <?php echo (int) $row['score']; ?>
</td>

<td class="<?php echo $class; ?>">
    <?php echo esc_html($row['message']); ?>
</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</section>