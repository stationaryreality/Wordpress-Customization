<?php
/*
|--------------------------------------------------------------------------
| Content Density Analyzer - Chapter & Fragment Sorter
|--------------------------------------------------------------------------
|
| Sorts chapters by number of attached CPTs (content density)
| and lists fragments that may be eligible for promotion to chapters.
|
*/

// Get all chapters with their content counts
$chapters = get_posts([
    'post_type'      => 'chapter',
    'posts_per_page' => -1,
    'post_status'    => 'publish'
]);

// Get all fragments
$fragments = get_posts([
    'post_type'      => 'fragment',
    'posts_per_page' => -1,
    'post_status'    => 'publish'
]);

// Function to count relationships for a post
function count_relationships($post_id) {
    $acf_fields = get_fields($post_id);
    $total_count = 0;
    
    if ($acf_fields) {
        foreach ($acf_fields as $field_name => $value) {
            // Count relationship fields (arrays of posts)
            if (is_array($value)) {
                foreach ($value as $item) {
                    if ($item instanceof WP_Post) {
                        $total_count++;
                    }
                }
            }
            // Count single relationship fields
            elseif ($value instanceof WP_Post) {
                $total_count++;
            }
        }
    }
    
    return $total_count;
}

// Calculate content density for chapters
$chapter_data = [];
foreach ($chapters as $chapter) {
    $chapter_data[] = [
        'id' => $chapter->ID,
        'title' => get_the_title($chapter->ID),
        'cpt_count' => count_relationships($chapter->ID),
        'date' => get_the_date('', $chapter->ID)
    ];
}

// Sort chapters by CPT count (highest first)
usort($chapter_data, function($a, $b) {
    return $b['cpt_count'] - $a['cpt_count'];
});

// Calculate content density for fragments
$fragment_data = [];
foreach ($fragments as $fragment) {
    $cpt_count = count_relationships($fragment->ID);
    $fragment_data[] = [
        'id' => $fragment->ID,
        'title' => get_the_title($fragment->ID),
        'cpt_count' => $cpt_count,
        'date' => get_the_date('', $fragment->ID),
        'ready_for_promotion' => $cpt_count >= 20
    ];
}

// Sort fragments by CPT count (highest first)
usort($fragment_data, function($a, $b) {
    return $b['cpt_count'] - $a['cpt_count'];
});

// Calculate statistics
$total_chapters = count($chapter_data);
$total_fragments = count($fragment_data);
$avg_chapter_density = $total_chapters > 0 ? array_sum(array_column($chapter_data, 'cpt_count')) / $total_chapters : 0;
$avg_fragment_density = $total_fragments > 0 ? array_sum(array_column($fragment_data, 'cpt_count')) / $total_fragments : 0;
$promotable_fragments = array_filter($fragment_data, function($f) {
    return $f['ready_for_promotion'];
});
?>

<section class="tool-content-density-analyzer">
    <style>
        .density-tool {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            max-width: 1400px;
            margin: 0 auto;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #f8f9fa;
            border-left: 4px solid #007cba;
            padding: 1rem;
            border-radius: 4px;
        }
        .stat-card h3 {
            margin: 0 0 0.5rem 0;
            font-size: 0.875rem;
            text-transform: uppercase;
            color: #666;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #1e1e1e;
        }
        .density-section {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .density-section h2 {
            margin-top: 0;
            border-bottom: 2px solid #007cba;
            padding-bottom: 0.5rem;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
        }
        .content-table th,
        .content-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .content-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #1e1e1e;
        }
        .content-table tr:hover {
            background: #f9f9f9;
        }
        .density-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .density-high {
            background: #d4edda;
            color: #155724;
        }
        .density-medium {
            background: #fff3cd;
            color: #856404;
        }
        .density-low {
            background: #f8d7da;
            color: #721c24;
        }
        .promotion-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #cce5ff;
            color: #004085;
        }
        .threshold-note {
            background: #e7f3ff;
            border-left: 4px solid #007cba;
            padding: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
    </style>

    <div class="density-tool">
        <header class="tool-header">
            <h1>📊 Content Density Analyzer</h1>
            <p>
                Sort chapters and fragments by content density (number of attached CPTs). 
                Fragments with 20+ CPTs are candidates for promotion to chapters.
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
                <h3>Ready for Promotion</h3>
                <div class="stat-number"><?php echo count($promotable_fragments); ?></div>
                <small>Fragments with ≥20 CPTs</small>
            </div>
        </div>

        <!-- Chapters Section -->
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

        <!-- Fragments Section -->
        <div class="density-section">
            <h2>📜 Fragments by Content Density</h2>
            <p>Sorted from highest to lowest number of attached CPTs</p>
            
            <?php if (count($fragment_data) > 0): ?>
                <div class="threshold-note">
                    💡 <strong>Promotion Tip:</strong> Fragments with 20 or more CPTs are strong candidates for promotion to chapters. 
                    The highest-density fragments would require the least work to convert.
                </div>
                
                <table class="content-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Fragment Title</th>
                            <th>Content Density</th>
                            <th>Status</th>
                            <th>Published Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fragment_data as $index => $fragment): 
                            $density_class = $fragment['cpt_count'] > 20 ? 'density-high' : ($fragment['cpt_count'] > 10 ? 'density-medium' : 'density-low');
                        ?>
                            <tr style="<?php echo $fragment['ready_for_promotion'] ? 'background-color: #f0f7ff;' : ''; ?>">
                                <td><?php echo $index + 1; ?></td>
                                <td><strong><?php echo esc_html($fragment['title']); ?></strong></td>
                                <td>
                                    <span class="density-badge <?php echo $density_class; ?>">
                                        <?php echo $fragment['cpt_count']; ?> CPTs
                                    </span>
                                </td>
                                <td>
                                    <?php if ($fragment['ready_for_promotion']): ?>
                                        <span class="promotion-badge">✓ Ready for Promotion (20+ CPTs)</span>
                                    <?php else: ?>
                                        <span style="color: #999;">Needs <?php echo (20 - $fragment['cpt_count']); ?> more CPTs</span>
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

        <!-- Quick Stats -->
        <div class="density-section">
            <h2>📈 Promotion Recommendations</h2>
            <?php
            $top_fragments = array_slice(array_filter($fragment_data, function($f) {
                return $f['ready_for_promotion'];
            }), 0, 10);
            
            if (count($top_fragments) > 0):
            ?>
                <h3>Top Fragments Ready for Promotion</h3>
                <ol>
                    <?php foreach ($top_fragments as $fragment): ?>
                        <li style="margin-bottom: 0.5rem;">
                            <strong><?php echo esc_html($fragment['title']); ?></strong> - 
                            <?php echo $fragment['cpt_count']; ?> CPTs
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php else: ?>
                <p>No fragments have reached the 20 CPT threshold yet. The highest density fragment has <?php echo !empty($fragment_data) ? $fragment_data[0]['cpt_count'] : 0; ?> CPTs.</p>
            <?php endif; ?>
            
            <hr style="margin: 1rem 0;">
            
            <p>
                <strong>Recommendation:</strong> 
                <?php if (count($top_fragments) > 0): ?>
                    Start by reviewing the fragments listed above. They already have substantial content density 
                    (20+ CPTs) and would be the strongest candidates for promotion to chapters.
                <?php else: ?>
                    Continue building content on your fragments. Focus on fragments with the highest current density 
                    as they're closest to the promotion threshold.
                <?php endif; ?>
            </p>
        </div>
    </div>
</section>