<?php
// theme-grid.php
// $args['items'] = array of ['image_id' => ID, 'title' => string, 'url' => string]
$items = $args['items'] ?? [];
$title = $args['title'] ?? 'Themes';
$emoji = $args['emoji'] ?? '';

if (empty($items)) return;
?>

<section style="margin-bottom:4rem;">
  <h2>
    <?php if ($emoji) echo esc_html($emoji) . ' '; ?>
    <?php echo esc_html($title); ?>
  </h2>

  <div class="cited-grid">
    <?php foreach ($items as $item): 
        $img_url = $item['image_id'] 
            ? wp_get_attachment_image_url($item['image_id'], 'medium') 
            : ''; 
    ?>
      <div class="cited-item" style="background:#fff; padding:0.5rem; border-radius:1rem; text-align:center;">
        <a href="<?php echo esc_url($item['url']); ?>">
          <?php if ($img_url): ?>
            <img src="<?php echo esc_url($img_url); ?>"
                 alt="<?php echo esc_attr($item['title']); ?>"
                 style="width:150px; height:150px; object-fit:cover; border-radius:0.5rem;">
          <?php else: ?>
            <div style="width:150px; height:150px; background:#000; border-radius:0.5rem;"></div>
          <?php endif; ?>
          <h3 style="margin-top:0.5rem; font-size:1rem;"><?php echo esc_html($item['title']); ?></h3>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</section>
