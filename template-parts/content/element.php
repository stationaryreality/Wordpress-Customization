<?php
$related = get_field('related_content');

if (!empty($related)) :
    $groups = [];
    foreach ($related as $item) {
        $type = get_post_type($item);
        if (in_array($type, ['chapter', 'fragment'])) continue;
        $groups[$type][] = $item;
    }

    if (!empty($groups)) :
?>
<div class="cpt-element-related">
    <details>
        <summary>Related Content</summary>
        <?php
        ksort($groups);
        foreach ($groups as $type => $items) :
            usort($items, fn($a, $b) => strcmp(get_the_title($a), get_the_title($b)));
            $meta = get_cpt_metadata($type);
        ?>
            <div class="cpt-element-related-group">
                <h4>
                    <?php echo esc_html($meta['emoji'] ?? '•'); ?>
                    <?php echo esc_html($meta['title'] ?? ucfirst($type)); ?>
                    (<?php echo count($items); ?>)
                </h4>
                <ul>
                    <?php foreach ($items as $item) : ?>
                        <li>
                            <a href="<?php echo esc_url(get_permalink($item)); ?>">
                                <?php echo esc_html(get_the_title($item)); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </details>
</div>
<?php
    endif;
endif;
?>