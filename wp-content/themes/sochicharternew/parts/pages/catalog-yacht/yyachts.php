<?php
// -------------
// Start to loop
$prefix = get_query_var('yyachts_posttype');
$args = array( 'post_type' => $prefix, 'posts_per_page' => 200 );
$loop = new WP_Query( $args );
while ( $loop->have_posts() ) : $loop->the_post();
?>

<?php 
    // --------------------
    // Get condition values
    $yPrice = intval(rwmb_meta($prefix . '-yacht_price'));
    $yClass = intval(rwmb_meta($prefix . '-yacht_class'));

    $cond = $GLOBALS['yclass_cond'];

    set_query_var('ycard_posttype', $prefix);
    
    if($cond == 'all') {
        get_template_part('parts/pages/catalog-yacht/ycard');
    }
    if($cond == 'econom') {
        if($yPrice <= 8000) {
            get_template_part('parts/pages/catalog-yacht/ycard');
        }
    }
    if($cond == 'business') {
        if($yPrice > 8000 && $yPrice <= 17000) {
            get_template_part('parts/pages/catalog-yacht/ycard');
        }
    }
    if($cond == 'vip') {
        if($yPrice > 17000) {
            get_template_part('parts/pages/catalog-yacht/ycard');
        }
    }
    if($cond == 'motor') {
        if($yClass == 1) {
            get_template_part('parts/pages/catalog-yacht/ycard');
        }
    }
    if($cond == 'sailing') {
        if($yClass == 2) {
            get_template_part('parts/pages/catalog-yacht/ycard');
        }
    }
    if($cond == 'katamaran') {
        if($yClass == 3) {
            get_template_part('parts/pages/catalog-yacht/ycard');
        }
    }
    if($cond == 'teplohod') {
        if($yClass == 4) {
            get_template_part('parts/pages/catalog-yacht/ycard');
        }
    }
    if($cond == 'kater') {
        if($yClass == 5) {
            get_template_part('parts/pages/catalog-yacht/ycard');
        }
    }
?>

<?php
endwhile;
?>