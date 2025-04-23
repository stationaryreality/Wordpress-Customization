<?php if ($chapter_notes = get_field('chapter_notes')): ?>
  <div class="chapter-text-block">
    <?php echo wp_kses_post($chapter_notes); ?>
  </div>


<div class="chapter-block">
  <!-- existing header block content -->
</div>


<?php endif; ?>
