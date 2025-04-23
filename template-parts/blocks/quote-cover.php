<?php
$quote_text = get_field('quote_text');
$attribution = get_field('attribution');
$headshot = get_field('headshot_image');
$background = get_field('background_image');
$overlay_opacity = get_field('overlay_opacity') ?: 50;
$quote_type = get_field('quote_type');
$footnote = get_field('footnote_text');

$is_wiki_or_movie = in_array($quote_type, ['wikipedia', 'movie']);
$has_headshot = $headshot && !in_array($quote_type, ['wikipedia', 'movie', 'lyrics']);
$headshot_class = $is_wiki_or_movie ? 'headshot-thumbnail right' : 'headshot-rounded centered';

// Dynamic overlay style — only for types that support it
$use_overlay = in_array($quote_type, ['lyrics', 'artist_lyric']);
$overlay_style = $use_overlay ? "background-color: rgba(0, 0, 0, " . ($overlay_opacity / 100) . ");" : "";

// Background image
$background_style = $background ? "background-image: url(" . esc_url($background['url']) . "); background-size: cover; background-position: center;" : "";

// Generate dynamic slug from attribution for headshot link
$slug = $attribution ? sanitize_title($attribution) : '';
$hub_link = $slug ? get_tag_link(get_term_by('slug', $slug, 'post_tag')) : '';
?>
<div class="quote-cover-block" style="<?php echo esc_attr($background_style); ?>">
    <?php if ($use_overlay): ?>
        <div class="quote-cover-overlay" style="<?php echo esc_attr($overlay_style); ?>"></div>
    <?php endif; ?>

    <div class="quote-cover-content">
        <?php if ($has_headshot): ?>
            <div class="quote-cover-headshot <?php echo esc_attr($headshot_class); ?>">
                <?php if ($hub_link): ?>
                    <a href="<?php echo esc_url($hub_link); ?>">
                        <img src="<?php echo esc_url($headshot['url']); ?>" alt="Headshot">
                    </a>
                <?php else: ?>
                    <img src="<?php echo esc_url($headshot['url']); ?>" alt="Headshot">
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($quote_text): ?>
            <blockquote class="quote-text"><?php echo esc_html($quote_text); ?></blockquote>
        <?php endif; ?>

        <?php if ($attribution): ?>
            <div class="quote-attribution"><?php echo esc_html($attribution); ?></div>
        <?php endif; ?>

        <?php if ($footnote): ?>
            <div class="quote-footnote"><?php echo esc_html($footnote); ?></div>
        <?php endif; ?>
    </div>
</div>
