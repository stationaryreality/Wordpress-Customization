<?php 

function ct_author_child_enqueue_styles() {

  $parent_style = 'ct-author-style';

  wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
  wp_enqueue_style( 'ct-author-child-style',
      get_stylesheet_directory_uri() . '/style.css',
      array( $parent_style )
  );
}
add_action( 'wp_enqueue_scripts', 'ct_author_child_enqueue_styles' );

add_post_type_support('page', 'excerpt');

#error_log('This is a custom test message');


add_action('load-edit.php', function () {
    $screen = get_current_screen();

    // Only affect posts (or change 'post' to your custom post type)
    if ($screen->post_type == 'post' && !isset($_GET['post_status']) && !isset($_GET['all_posts'])) {
        wp_redirect(admin_url('edit.php?post_status=publish&post_type=post'));
        exit;
    }
});


function get_blocks_by_anchor($target_anchors = []) {
    $matching_blocks = [];

    $args = [

	'post_type' => ['post'],
	'posts_per_page' => -1,
        'post_status'    => 'publish',
    ];
    $query = new WP_Query($args);

    while ($query->have_posts()) {
        $query->the_post();
        $blocks = parse_blocks(get_the_content());

        foreach ($blocks as $block) {
            if (!empty($block['attrs']['anchor']) && in_array($block['attrs']['anchor'], $target_anchors)) {
                $matching_blocks[] = $block;
            }
        }
    }

    wp_reset_postdata();
    return $matching_blocks;
}


function load_block_editor_styles_frontend() {
    if (is_page_template('template-lexicon.php')) {
        // Ensures global block styles (like Cover, Paragraph, etc.) are loaded
        wp_enqueue_style('wp-block-library');
	wp_enqueue_style('wp-block-library-theme');

    }
}
add_action('wp_enqueue_scripts', 'load_block_editor_styles_frontend');


add_action('acf/init', 'register_acf_photo_block');
function register_acf_photo_block() {
    if( function_exists('acf_register_block_type') ) {
        acf_register_block_type([
            'name'              => 'photo-block',
            'title'             => 'Photo Block',
            'description'       => 'A block that displays an image with a caption and source.',
            'render_template'   => 'template-parts/blocks/photo-block.php',
            'category'          => 'formatting',
            'icon'              => 'format-image',
            'keywords'          => ['photo', 'image', 'caption'],
            'mode'              => 'preview',
            'supports'          => [
                'align' => ['full', 'wide']
            ],
        ]);
    }
}




add_action('acf/init', 'register_chapter_header_block');
function register_chapter_header_block() {
    if( function_exists('acf_register_block_type') ) {
        acf_register_block_type(array(
            'name'              => 'chapter-header',
            'title'             => __('Chapter Header'),
            'description'       => __('Displays artist headshot, name, and song title.'),
            'render_template'   => get_theme_file_path('/template-parts/blocks/chapter-header.php'),
            'category'          => 'formatting',
            'icon'              => 'format-image',
            'keywords'          => array('chapter', 'header', 'artist'),
            'mode'              => 'edit',
            'supports'          => array(
                'align' => true,
                'mode' => true,
                'jsx' => true
            ),
        ));
    }
}


add_action('acf/init', function () {
    acf_register_block_type([
        'name'            => 'chapter-notes',
        'title'           => 'Chapter Notes',
        'render_template' => 'template-parts/blocks/chapter-notes.php',
        // Add rest of your config here...
    ]);
});





add_action('acf/init', function () {
    acf_register_block_type([
        'name' => 'quote-cover',
        'title' => 'Quote Cover Block',
        'description' => 'Displays a stylized quote with background, headshot, and attribution.',
        'render_template' => 'template-parts/blocks/quote-cover.php',
        'category' => 'formatting',
        'icon' => 'format-quote',
        'keywords' => ['quote', 'cover', 'author'],
        'mode' => 'preview',
        'supports' => [
            'align' => false,
        ]
    ]);
});



function render_quote_cover_block($block) {
    $quote_text = get_field('quote_text');
    $attribution = get_field('attribution');
    $headshot = get_field('headshot_image');
    $background = get_field('background_image');
    $overlay_opacity = get_field('overlay_opacity');
    $quote_type = get_field('quote_type');
    $footnote = get_field('footnote_text');

    $is_wiki_style = in_array($quote_type, ['wikipedia', 'movie']);
    $headshot_class = $is_wiki_style ? 'headshot-thumbnail right' : 'headshot-rounded centered';

    // Include the template file manually
    include get_theme_file_path('/template-parts/blocks/quote-cover.php');
}



add_action('acf/init', function () {
    acf_register_block_type([
        'name' => 'cover-block',
        'title' => 'Cover Block',
        'description' => 'Generic base cover block with optional background and overlay.',
        'render_template' => 'template-parts/blocks/cover-block.php',
        'category' => 'formatting',
        'icon' => 'cover-image',
        'keywords' => ['cover', 'background', 'overlay'],
        'mode' => 'preview',
        'supports' => [
            'align' => true,
            'anchor' => true,
        ],
    ]);
});


