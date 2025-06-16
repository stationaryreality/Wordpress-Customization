<?php
$person_type = get_the_terms(get_the_ID(), 'person_type');
$bio = get_field('bio'); // Optional ACF field
$portrait = get_field('portrait_image', get_the_ID());
$img_url  = $portrait ? $portrait['sizes']['medium'] : '';

?>

<div class="person-content">
<?php if ($img_url): ?>
  <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="author-thumbnail">
<?php endif; ?>


  <h1><?php the_title(); ?></h1>

  <?php if (!empty($person_type) && !is_wp_error($person_type)): ?>
    <p>
      <strong>
        <?php echo esc_html(implode(', ', wp_list_pluck($person_type, 'name'))); ?>
      </strong>
    </p>
  <?php endif; ?>

  <div class="person-bio">
    <?php if ($bio): ?>
      <?php echo wp_kses_post($bio); ?>
    <?php else: ?>
      <?php the_content(); ?>
    <?php endif; ?>
  </div>

  <?php get_template_part('content/person-nav'); ?>
</div>
