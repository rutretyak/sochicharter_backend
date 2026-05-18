<div class="content">
<?php 
$svg = get_query_var('content_svg');
if($svg == 'motor') {
?>
<?php /*
    <div class="content__svg">
    <?php get_template_part('svg/catalog-yaht/motornye') ?>
    </div>
    */ ?>
<?php
}
?>
<div class="container">

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <div class="row">
            
                <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
                    <div class="ycontent__content">
                        <?php the_content('', FALSE); ?>
                    </div>
                </div>

                <div class="ycontent__sidebar col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
                    <?php 
                        set_query_var('yaside_posttype', get_query_var('content_posttype'));
                        get_template_part('parts/pages/catalog-yacht/yaside');
                    ?>
                </div>

            </div>
        <?php endwhile; endif; ?>

    </div>
</div>