<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php
$prefix = get_post_meta($post->ID, 'cy_posttype', true);
$h1 = get_post_meta($post->ID, 'cy_h1', true);
?>
<?php endwhile; endif; ?>

<div class="page page_inner page_yacht">

<div class="yacht">
    <div class="container">

        <div class="row">
            <h1><?= $h1 ?></h1>
        </div>

        <?php 
/*             if($prefix == 'yachts') {
                get_template_part('parts/pages/catalog-yacht/yclass');
            }  */
        ?>

        <div class="cy__wrap row">
            <?php 
                //set_query_var('yyachts_posttype', $prefix);
                //get_template_part('parts/pages/catalog-yacht/yyachts') 
            ?>
            <?php 
                $pricefrom = $GLOBALS['yclass_pricefrom'] ? $GLOBALS['yclass_pricefrom'] : '';
                $priceto = $GLOBALS['yclass_priceto'] ? $GLOBALS['yclass_priceto'] : '';
                $type = $GLOBALS['yclass_type'] ? $GLOBALS['yclass_type'] : '';

                set_query_var('showcase_posttype', $prefix);
                set_query_var('c_pricefrom', $pricefrom);
                set_query_var('c_priceto', $priceto);
                set_query_var('c_capacity', '');
                set_query_var('c_cabins', '');
                set_query_var('c_type', $type);

                get_template_part('parts/blocks/showcase/showcase');
            ?>
        </div>
    </div>
</div>

<div class="ycontent">
    <div class="container">
        <div class="row">
            
            <div class="col-xl-8 col-lg-7 col-md-12 col-sm-12 col-12">
                <div class="ycontent__content">
                    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    <?php the_content('', FALSE); ?>
                    <?php endwhile; endif; ?>
                </div>
            </div>

            <div class="col-xl-4 col-lg-5 col-md-12 col-sm-12 col-12">
                <?php 
                    set_query_var('formname', 'main-form');
                    set_query_var('formclass', 'main__form yacht__form');
                    set_query_var('form_title', 'Оставьте заявку');
                    set_query_var('form_subtitle', 'Перезвоним &lt; 10 мин!');
                    set_query_var('form_cta', 'Забронировать!');
                    set_query_var('forminfo', 'Главная форма');
                    set_query_var('form_inputname', true);
                    set_query_var('form_inputphone', true);
                    set_query_var('form_inputname_id', 'main__inputname');
                    set_query_var('form_inputphone_id', 'main__inputphone');
                    set_query_var('form_asterisk', '* консультируем бесплатно');
                    get_template_part('parts/forms/mainform');
                ?>
                <?php 
                    set_query_var('yaside_posttype', $prefix);
                    get_template_part('parts/pages/catalog-yacht/yaside');
                ?>
            </div>

        </div>
    </div>
</div>

</div>
