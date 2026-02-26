<?php
/* Template Name: Newest Content (Chronological) */
get_header();

$map = get_cpt_metadata();

$post_types = [
 'artist','profile','book','concept','movie','quote','lyric',
 'reference','organization','image','song','chapter',
 'excerpt','fragment','element'
];

$icons = [];
foreach ($post_types as $pt) {
    $icons[$pt] = $map[$pt]['emoji'] ?? '❓';
}

/* ===== QUERY POSTS ===== */
$q = new WP_Query([
 'post_type'=>$post_types,
 'posts_per_page'=>-1,
 'orderby'=>'date',
 'order'=>'DESC',
 'post_status'=>'publish'
]);

$tree = [];
$total_count = 0;

/* ===== POSTS ===== */
while ($q->have_posts()) {
$q->the_post();

$t = get_post_time('U');

$y=date('Y',$t);
$m=date('n',$t);
$d=date('j',$t);

$tree[$y][$m][$d][]=[
 'title'=>get_the_title(),
 'url'=>get_permalink(),
 'time'=>date('H:i',$t),
 'icon'=>$icons[get_post_type()]
];

$total_count++;
}
wp_reset_postdata();

/* ===== TAXONOMY TERMS ===== */
foreach (['theme','topic'] as $tax) {

  $terms = get_terms([
    'taxonomy'=>$tax,
    'hide_empty'=>false
  ]);

  foreach ($terms as $t_obj) {

    // Use term creation proxy (first associated post date)
    $term_posts = get_posts([
        'post_type'=>$post_types,
        'posts_per_page'=>1,
        'orderby'=>'date',
        'order'=>'DESC',
        'tax_query'=>[
            [
                'taxonomy'=>$tax,
                'field'=>'term_id',
                'terms'=>$t_obj->term_id
            ]
        ]
    ]);

    if (!$term_posts) continue;

    $t = get_post_time('U', false, $term_posts[0]);

    $y=date('Y',$t);
    $m=date('n',$t);
    $d=date('j',$t);

    $meta  = get_cpt_metadata($tax);
    $emoji = $meta['emoji'] ?? '•';

    $tree[$y][$m][$d][]=[
      'title'=>$t_obj->name,
      'url'=>get_term_link($t_obj),
      'time'=>date('H:i',$t),
      'icon'=>$emoji
    ];

    $total_count++;
  }
}

/* ===== FORCE ORDER ===== */
krsort($tree);
foreach ($tree as &$months){
    krsort($months);
    foreach ($months as &$days){
        krsort($days);
    }
}
?>

<main class="cpt-index-chronological">

<header class="archive-header">
<h1>Newest Content</h1>

<p class="cpt-total" style="margin:0.75em 0 1.5em 0;">
<?php echo number_format($total_count); ?> total entries
</p>

<p><a href="/site-index/">← Alphabetical Index</a></p>
</header>

<?php foreach ($tree as $year=>$months): ?>
<h2><?php echo $year; ?></h2>

<?php foreach ($months as $month=>$days): ?>
<h3><?php echo date('F', mktime(0,0,0,$month,1)); ?></h3>

<?php foreach ($days as $day=>$items): ?>
<h4><?php echo $day; ?></h4>

<ul>
<?php foreach ($items as $item): ?>
<li>
<span class="cpt-icon"><?php echo $item['icon']; ?></span>
<a href="<?php echo $item['url']; ?>"><?php echo $item['title']; ?></a>
</li>
<?php endforeach; ?>
</ul>

<?php endforeach; ?>
<?php endforeach; ?>
<?php endforeach; ?>

</main>

<?php get_footer(); ?>