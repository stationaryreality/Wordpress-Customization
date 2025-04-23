<?php
// ACF fields
$quote_text = get_field('quote_text');
$attribution = get_field('attribution');
$headshot = get_field('headshot_image');
$background = get_field('background_image');
$overlay_opacity = get_field('overlay_opacity');
$quote_type = get_field('quote_type');
$footnote = get_field('footnote_text');

// Headshot style logic
$is_wiki_style = in_array($quote_type, ['wikipedia', 'movie']);
$headshot_class = $is_wiki_style ? 'headshot-thumbnail right' : 'headshot-rounded centered';

// Overlay opacity logic
$opacity_value = is_numeric($overlay_opacity) ? $overlay_opacity / 100 : 0.5;
?>

<div class="quote-cover-block"
     <?php if (!empty($background['url'])): ?>
         style="background-image: url('<?php echo esc_url($background['url']); ?>');"
     <?php endif; ?>>

    <div class="overlay" style="opacity: <?php echo esc_attr($opacity_value); ?>;"></div>

    <div class="content">
        <?php if ($headshot): ?>
            <img src="<?php echo esc_url($headshot['url']); ?>" class="<?php echo esc_attr($headshot_class); ?>" alt="">
        <?php endif; ?>

        <?php if ($quote_text): ?>
            <blockquote class="quote-text"><?php echo esc_html($quote_text); ?></blockquote>
        <?php endif; ?>

        <?php if ($attribution): ?>
            <div class="attribution"><?php echo esc_html($attribution); ?></div>
        <?php endif; ?>

        <?php if ($footnote): ?>
            <div class="footnote"><?php echo esc_html($footnote); ?></div>
        <?php endif; ?>
    </div>
</div>
