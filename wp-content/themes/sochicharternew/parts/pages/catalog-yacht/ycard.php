<?php
// ----------------------------
// Get thumbnail image alt text
$thumbId  = get_post_thumbnail_id($post->ID);
$thumbAlt = get_post_meta($thumbId, '_wp_attachment_image_alt', true);
$prefix = get_query_var('ycard_posttype');
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