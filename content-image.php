<?php
$image_id = get_the_ID();
$caption  = get_field('image_caption', $image_id);
$image    = get_field('image_file', $image_id);
$img_large_url  = $image ? $image['sizes']['large'] : '';
$img_full_url   = $image ? $image['url'] : '';
?>

<div class="image-header" style="text-align:center;">
  <?php if ($img_large_url): ?>
    <a href="<?php echo esc_url($img_full_url); ?>" class="lightbox-link">
      <img src="<?php echo esc_url($img_large_url); ?>" alt="<?php the_title(); ?>" style="max-width:600px; width:100%; height:auto; display:block; margin:0 auto 1em;">
    </a>
  <?php endif; ?>
  <h1><?php the_title(); ?></h1>
</div>

<div class="image-caption" style="text-align:center;">
  <?php if ($caption): ?>
    <?php echo wp_kses_post($caption); ?>
  <?php else: ?>
    <?php the_content(); ?>
  <?php endif; ?>
</div>

<?php show_featured_in_threads('images_linked'); ?>

<div style="text-align:center;">
  <?php echo fn_taxonomy_bubbles(get_the_ID()); ?>
</div>

<?php get_template_part('content/image-nav'); ?>

<!-- Simple built-in lightbox -->
<style>
  .lightbox-overlay {
    display: none;
    position: fixed;
    z-index: 9999;
    inset: 0;
    background: rgba(0,0,0,0.9);
    justify-content: center;
    align-items: center;
  }
  .lightbox-overlay img {
    max-width: 95%;
    max-height: 95%;
    border-radius: 8px;
  }
  .lightbox-overlay.active {
    display: flex;
  }
  .lightbox-overlay::after {
    content: "✕";
    position: absolute;
    top: 20px;
    right: 30px;
    color: white;
    font-size: 28px;
    cursor: pointer;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.createElement('div');
    overlay.className = 'lightbox-overlay';
    document.body.appendChild(overlay);

    const img = document.createElement('img');
    overlay.appendChild(img);

    overlay.addEventListener('click', () => {
      overlay.classList.remove('active');
      img.src = '';
    });

    document.querySelectorAll('.lightbox-link').forEach(link => {
      link.addEventListener('click', e => {
        e.preventDefault();
        img.src = link.href;
        overlay.classList.add('active');
      });
    });
  });
</script>
