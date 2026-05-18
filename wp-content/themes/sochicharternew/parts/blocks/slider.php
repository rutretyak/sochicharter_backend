<div class="yacht yslider">
    <div class="container">
        

        <div class="row yslider__title">
            <a href='<?= get_query_var('slider_allhref') ?>' title='<?= get_query_var('slider_allalt') ?>'>Смотреть все яхты &raquo;</a>
        </div>

        <div class="row">
<?php // Yacht slider ?>
<div class="yacht__slider w-100">
    <div class="yacht__list"
    data-type="slick"
    data-lazyload="ondemand"
    data-speed="900"
    data-infinite-md="true"
    data-infinite-sm="true"
    data-infinite-xs="true"
    data-stshow-md="4"
    data-stshow-sm="3"
    data-stshow-xs="1"
    data-stscroll-md="4"
    data-stscroll-sm="3"
    data-stscroll-xs="1"
    data-dots-md="true"
    data-dots-sm="true"
    data-dots-xs="true"
    data-adaptiveheight="true">

<?php
    // -------------
    // Start to loop
    $prefix = get_query_var('slider_posttype');
    $args = array( 'post_type' => get_query_var('slider_posttype'), 'posts_per_page' => 200 );
    $loop = new WP_Query( $args );
    while ( $loop->have_posts() ) : $loop->the_post();
    $ystatus = rwmb_meta($prefix . '-yachts_status') == 1 ? 'ycard_available' : 'ycard_unavailable';
?>

    <a class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12 ycard <?= $ystatus ?>" href="<?= the_permalink() ?>" title="<?= the_title() ?>">
        <div class="ycard__block">
        
            <div class="ycard__iwrap d-flex">
                <img src="<?= get_the_post_thumbnail_url($post->ID, 'medium') ?>" alt="<?= $thumbAlt ?>" class="img-fluid">
            </div>

            <span class="ycard__title w-100 d-block"><?= rwmb_meta($prefix . '-yacht_name') ?></span>
            
            <div class="ycard__info d-flex justify-content-between">

                <div class="ycard__infol">
                    <?php get_template_part('svg/price') ?>
                    <span class="ycard__price"><?= rwmb_meta($prefix . '-yacht_price') ?></span>
                    <span class="ycard__pricetxt">Р/час</span>
                </div>

                <div class="ycard__infor">
                    <?php get_template_part('svg/capacity') ?>
                    <span class="ycard__capacity"><?= rwmb_meta($prefix . '-yacht_capacity') ?></span>
                    <span class="ycard__capacitytxt">чел</span>
                </div>

            </div>

        </div>
    </a>

<?php
    endwhile;
?>

</div>
</div>
<?php // #Yacht slider ?>

</div>
</div>
</div>