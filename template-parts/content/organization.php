<?php
$org_id    = get_the_ID();
$bio       = get_field('org_bio', $org_id);
$logo      = get_field('cover_image', $org_id);
$img_url   = $logo ? $logo['sizes']['thumbnail'] : '';
$wiki_slug = get_field('wikipedia_slug', $org_id);
$people    = get_field('related_people', $org_id); // ACF relationship or repeater field

?>

<div class="organization-header" style="text-align:center;">
  <?php if ($img_url): ?>
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="author-thumbnail" style="border-radius:0; aspect-ratio:1/1; object-fit:cover; max-width:300px; margin-bottom:1em;">
  <?php endif; ?>
  <h1><?php the_title(); ?></h1>
</div>

<div class="organization-bio" style="text-align:center;">
  <?php if ($bio): ?>
    <?php echo wp_kses_post($bio); ?>
  <?php elseif ($wiki_slug): ?>
    <p><?php echo kp_get_wikipedia_intro($wiki_slug); ?></p>
  <?php else: ?>
    <?php the_content(); ?>
  <?php endif; ?>
</div>

<?php if ($people): ?>
  <div class="related-people" style="margin-top:3em; text-align:center;">
    <h2>Related People</h2>
    <ul style="list-style:none; padding:0; display:inline-block; text-align:left;">
      <?php foreach ($people as $person): ?>
        <li>
          <a href="<?php echo get_permalink($person->ID); ?>">
            <?php echo esc_html(get_the_title($person->ID)); ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php show_featured_in_threads('organizations_referenced'); ?>

<?php get_template_part('content/organization-nav'); ?>
