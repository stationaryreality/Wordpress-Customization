<?php
$org_id    = get_the_ID();
$bio       = get_field('org_bio', $org_id);
$logo      = get_field('cover_image', $org_id);
$img_url   = $logo ? $logo['sizes']['thumbnail'] : '';
$wiki_slug = get_field('wikipedia_slug', $org_id);
$people    = get_field('related_people', $org_id);
?>

<div class="cpt-organization-content">

    <?php get_template_part('template-parts/navigation/organization'); ?>

    <div class="cpt-organization-header">
        <?php if ($img_url): ?>
            <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="cpt-organization-logo">
        <?php endif; ?>
        <h1 class="cpt-organization-title"><?php the_title(); ?></h1>
    </div>

    <div class="cpt-organization-bio">
        <?php if ($bio): ?>
            <?php echo wp_kses_post($bio); ?>
        <?php elseif ($wiki_slug): ?>
            <p><?php echo wp_kses_post(kp_get_wikipedia_intro($wiki_slug)); ?></p>
        <?php else: ?>
            <?php the_content(); ?>
        <?php endif; ?>
    </div>

    <?php if ($people): ?>
        <div class="cpt-organization-related">
            <h2 class="cpt-organization-related-title">Related People</h2>
            <ul class="cpt-organization-related-list">
                <?php foreach ($people as $person): ?>
                    <li class="cpt-organization-related-item">
                        <a href="<?php echo esc_url(get_permalink($person->ID)); ?>">
                            <?php echo esc_html(get_the_title($person->ID)); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php show_featured_in_threads('organizations_referenced'); ?>

</div>