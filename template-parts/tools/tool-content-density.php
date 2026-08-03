<?php
/*
|--------------------------------------------------------------------------
| Content Density Analyzer - Chapter, Fragment & Element Sorter
|--------------------------------------------------------------------------
*/

// ===== CONFIGURABLE THRESHOLDS =====
$element_to_fragment_threshold = 15;   // Elements with ≥ this many CPTs → promote to Fragment
$fragment_to_element_threshold = 10;   // Fragments with ≤ this many CPTs → demote to Element
$fragment_to_chapter_threshold = 20;   // Fragments with ≥ this many CPTs → promote to Chapter

// Get all chapters, fragments, and elements
$chapters = get_posts([
    'post_type'      => 'chapter',
    'posts_per_page' => -1,
    'post_status'    => 'publish'
]);

$fragments = get_posts([
    'post_type'      => 'fragment',
    'posts_per_page' => -1,
    'post_status'    => 'publish'
]);

$elements = get_posts([
    'post_type'      => 'element',
    'posts_per_page' => -1,
    'post_status'    => 'publish'
]);

// Function to count relationships for a post
function count_relationships($post_id) {
    $acf_fields = get_fields($post_id);
    $total_count = 0;
    
    if ($acf_fields) {
        foreach ($acf_fields as $field_name => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    if ($item instanceof WP_Post) {
                        $total_count++;
                    }
                }
            } elseif ($value instanceof WP_Post) {
                $total_count++;
            }
        }
    }
    return $total_count;
}

// ---- Chapters ----
$chapter_data = [];
foreach ($chapters as $chapter) {
    $chapter_data[] = [
        'id'        => $chapter->ID,
        'title'     => get_the_title($chapter->ID),
        'cpt_count' => count_relationships($chapter->ID),
        'date'      => get_the_date('', $chapter->ID)
    ];
}
usort($chapter_data, function($a, $b) {
    return $b['cpt_count'] - $a['cpt_count'];
});

// ---- Fragments ----
$fragment_data = [];
foreach ($fragments as $fragment) {
    $cpt_count = count_relationships($fragment->ID);
    $fragment_data[] = [
        'id'            => $fragment->ID,
        'title'         => get_the_title($fragment->ID),
        'cpt_count'     => $cpt_count,
        'date'          => get_the_date('', $fragment->ID),
        'ready_for_chapter' => $cpt_count >= $fragment_to_chapter_threshold,
        'ready_for_element'  => $cpt_count <= $fragment_to_element_threshold
    ];
}
usort($fragment_data, function($a, $b) {
    return $b['cpt_count'] - $a['cpt_count'];
});

// ---- Elements ----
$element_data = [];
foreach ($elements as $element) {
    $cpt_count = count_relationships($element->ID);
    $element_data[] = [
        'id'            => $element->ID,
        'title'         => get_the_title($element->ID),
        'cpt_count'     => $cpt_count,
        'date'          => get_the_date('', $element->ID),
        'ready_for_fragment' => $cpt_count >= $element_to_fragment_threshold
    ];
}
usort($element_data, function($a, $b) {
    return $b['cpt_count'] - $a['cpt_count'];
});

// ---- Statistics ----
$total_chapters   = count($chapter_data);
$total_fragments  = count($fragment_data);
$total_elements   = count($element_data);

$avg_chapter_density  = $total_chapters  > 0 ? array_sum(array_column($chapter_data, 'cpt_count')) / $total_chapters  : 0;
$avg_fragment_density = $total_fragments > 0 ? array_sum(array_column($fragment_data, 'cpt_count')) / $total_fragments : 0;
$avg_element_density  = $total_elements  > 0 ? array_sum(array_column($element_data, 'cpt_count')) / $total_elements  : 0;

$promotable_fragments = array_filter($fragment_data, function($f) {
    return $f['ready_for_chapter'];
});
$demotable_fragments  = array_filter($fragment_data, function($f) {
    return $f['ready_for_element'];
});
$promotable_elements  = array_filter($element_data, function($e) {
    return $e['ready_for_fragment'];
});
?>

<section class="tool-content-density-analyzer">
    <div class="density-tool">
        <header class="tool-header">
            <h1>📊 Content Density Analyzer</h1>
            <p>
                Sort chapters, fragments, and elements by content density (number of attached CPTs).
                Thresholds: <?php echo $element_to_fragment_threshold; ?>+ CPTs for Element → Fragment,
                ≤<?php echo $fragment_to_element_threshold; ?> CPTs for Fragment → Element,
                and <?php echo $fragment_to_chapter_threshold; ?>+ CPTs for Fragment → Chapter.
            </p>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Chapters</h3>
                <div class="stat-number"><?php echo $total_chapters; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Fragments</h3>
                <div class="stat-number"><?php echo $total_fragments; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Elements</h3>
                <div class="stat-number"><?php echo $total_elements; ?></div>
            </div>
            <div class="stat-card">
                <h3>Avg Chapter Density</h3>
                <div class="stat-number"><?php echo round($avg_chapter_density, 1); ?></div>
                <small>CPTs per chapter</small>
            </div>
            <div class="stat-card">
                <h3>Avg Fragment Density</h3>
                <div class="stat-number"><?php echo round($avg_fragment_density, 1); ?></div>
                <small>CPTs per fragment</small>
            </div>
            <div class="stat-card">
                <h3>Avg Element Density</h3>
                <div class="stat-number"><?php echo round($avg_element_density, 1); ?></div>
                <small>CPTs per element</small>
            </div>
            <div class="stat-card">
                <h3>Ready for Promotion</h3>
                <div class="stat-number"><?php echo count($promotable_fragments); ?></div>
                <small>Fragments ≥<?php echo $fragment_to_chapter_threshold; ?> CPTs</small>
            </div>
            <div class="stat-card">
                <h3>Demotion Candidates</h3>
                <div class="stat-number"><?php echo count($demotable_fragments); ?></div>
                <small>Fragments ≤<?php echo $fragment_to_element_threshold; ?> CPTs</small>
            </div>
            <div class="stat-card">
                <h3>Elements → Fragments</h3>
                <div class="stat-number"><?php echo count($promotable_elements); ?></div>
                <small>Elements ≥<?php echo $element_to_fragment_threshold; ?> CPTs</small>
            </div>
        </div>

        <!-- ===== CHAPTERS SECTION ===== -->
        <div class="density-section">
            <h2>📚 Chapters by Content Density</h2>
            <p>Sorted from highest to lowest number of attached CPTs</p>
            
            <?php if (count($chapter_data) > 0): ?>
                <table class="content-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Chapter Title</th>
                            <th>Content Density</th>
                            <th>Published Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($chapter_data as $index => $chapter): 
                            $density_class = $chapter['cpt_count'] > 20 ? 'density-high' : ($chapter['cpt_count'] > 10 ? 'density-medium' : 'density-low');
                        ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><strong><?php echo esc_html($chapter['title']); ?></strong></td>
                                <td>
                                    <span class="density-badge <?php echo $density_class; ?>">
                                        <?php echo $chapter['cpt_count']; ?> CPTs
                                    </span>
                                </td>
                                <td><?php echo esc_html($chapter['date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No chapters found.</p>
            <?php endif; ?>
        </div>

        <!-- ===== FRAGMENTS SECTION ===== -->
        <div class="density-section">
            <h2>📜 Fragments by Content Density</h2>
            <p>Sorted from highest to lowest number of attached CPTs</p>
            
            <?php if (count($fragment_data) > 0): ?>
                <div class="threshold-note">
                    💡 <strong>Promotion Tip:</strong> Fragments with <?php echo $fragment_to_chapter_threshold; ?>+ CPTs are strong candidates for promotion to chapters.<br>
                    🔽 <strong>Demotion Tip:</strong> Fragments with ≤<?php echo $fragment_to_element_threshold; ?> CPTs may be better off as elements.
                </div>
                
                <table class="content-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Fragment Title</th>
                            <th>Content Density</th>
                            <th>Recommendation</th>
                            <th>Published Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fragment_data as $index => $fragment): 
                            $density_class = $fragment['cpt_count'] > 20 ? 'density-high' : ($fragment['cpt_count'] > 10 ? 'density-medium' : 'density-low');
                        ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><strong><?php echo esc_html($fragment['title']); ?></strong></td>
                                <td>
                                    <span class="density-badge <?php echo $density_class; ?>">
                                        <?php echo $fragment['cpt_count']; ?> CPTs
                                    </span>
                                </td>
                                <td>
                                    <?php if ($fragment['ready_for_chapter']): ?>
                                        <span class="promotion-badge">↑ Promote to Chapter</span>
                                    <?php elseif ($fragment['ready_for_element']): ?>
                                        <span class="demotion-badge">↓ Demote to Element</span>
                                    <?php else: ?>
                                        <span class="status-keep">Keep as Fragment</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html($fragment['date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No fragments found.</p>
            <?php endif; ?>
        </div>

        <!-- ===== ELEMENTS SECTION ===== -->
        <div class="density-section">
            <h2>🧩 Elements by Content Density</h2>
            <p>Sorted from highest to lowest number of attached CPTs</p>
            
            <?php if (count($element_data) > 0): ?>
                <div class="threshold-note">
                    💡 <strong>Promotion Tip:</strong> Elements with <?php echo $element_to_fragment_threshold; ?>+ CPTs are candidates for promotion to fragments.
                </div>
                
                <table class="content-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Element Title</th>
                            <th>Content Density</th>
                            <th>Recommendation</th>
                            <th>Published Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($element_data as $index => $element): 
                            $density_class = $element['cpt_count'] >= $element_to_fragment_threshold ? 'density-high' : ($element['cpt_count'] > 5 ? 'density-medium' : 'density-low');
                        ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><strong><?php echo esc_html($element['title']); ?></strong></td>
                                <td>
                                    <span class="density-badge <?php echo $density_class; ?>">
                                        <?php echo $element['cpt_count']; ?> CPTs
                                    </span>
                                </td>
                                <td>
                                    <?php if ($element['ready_for_fragment']): ?>
                                        <span class="promotion-badge">↑ Promote to Fragment</span>
                                    <?php else: ?>
                                        <span class="status-keep">Keep as Element</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html($element['date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No elements found.</p>
            <?php endif; ?>
        </div>

        <!-- ===== RECOMMENDATIONS SUMMARY ===== -->
        <div class="density-section">
            <h2>📈 Promotion & Demotion Recommendations</h2>
            
            <div class="recommendation-grid">
                <!-- Promote to Chapter -->
                <div class="recommendation-box">
                    <h3 style="margin-top:0;">Fragments → Chapters</h3>
                    <?php
                    $top_chapter_candidates = array_slice(array_filter($fragment_data, function($f) {
                        return $f['ready_for_chapter'];
                    }), 0, 10);
                    if (count($top_chapter_candidates) > 0): ?>
                        <ul>
                            <?php foreach ($top_chapter_candidates as $f): ?>
                                <li><strong><?php echo esc_html($f['title']); ?></strong> (<?php echo $f['cpt_count']; ?> CPTs)</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No fragments have reached the <?php echo $fragment_to_chapter_threshold; ?>‑CPT threshold yet.</p>
                    <?php endif; ?>
                </div>

                <!-- Demote to Element -->
                <div class="recommendation-box recommendation-box--demotion">
                    <h3 style="margin-top:0;">Fragments → Elements</h3>
                    <?php
                    $top_demotion_candidates = array_slice(array_filter($fragment_data, function($f) {
                        return $f['ready_for_element'];
                    }), 0, 10);
                    if (count($top_demotion_candidates) > 0): ?>
                        <ul>
                            <?php foreach ($top_demotion_candidates as $f): ?>
                                <li><strong><?php echo esc_html($f['title']); ?></strong> (<?php echo $f['cpt_count']; ?> CPTs)</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No fragments have ≤<?php echo $fragment_to_element_threshold; ?> CPTs.</p>
                    <?php endif; ?>
                </div>

                <!-- Elements → Fragments -->
                <div class="recommendation-box recommendation-box--element-promotion">
                    <h3 style="margin-top:0;">Elements → Fragments</h3>
                    <?php
                    $top_element_candidates = array_slice(array_filter($element_data, function($e) {
                        return $e['ready_for_fragment'];
                    }), 0, 10);
                    if (count($top_element_candidates) > 0): ?>
                        <ul>
                            <?php foreach ($top_element_candidates as $e): ?>
                                <li><strong><?php echo esc_html($e['title']); ?></strong> (<?php echo $e['cpt_count']; ?> CPTs)</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No elements have reached the <?php echo $element_to_fragment_threshold; ?>‑CPT threshold yet.</p>
                    <?php endif; ?>
                </div>
            </div>

            <hr class="divider">
            <p>
                <strong>Summary:</strong> 
                <?php
                $actions = [];
                if (count($top_chapter_candidates) > 0) $actions[] = 'promote ' . count($top_chapter_candidates) . ' fragments to chapters';
                if (count($top_demotion_candidates) > 0) $actions[] = 'demote ' . count($top_demotion_candidates) . ' fragments to elements';
                if (count($top_element_candidates) > 0) $actions[] = 'promote ' . count($top_element_candidates) . ' elements to fragments';
                if (empty($actions)) {
                    echo 'No immediate actions required – keep building content.';
                } else {
                    echo 'Consider: ' . implode('; ', $actions) . '.';
                }
                ?>
            </p>
        </div>
    </div>
</section>