<?php /* Template Name: Adler Ceny */ ?>

<?php get_header() ?>

<div class="page page_inner page_yacht page_simple page_ceny">

    <div class="yacht">
        <div class="container">
            <div class="row">
                <h1>
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <?= the_title() ?>
                <?php endwhile; endif; ?>
                </h1>
            </div>
        </div>
    </div>

    <div class="ycontent ycontent_cut">

        <div class="container">
            <div class="row">
            
                <div class="col-xl-8 col-lg-7 col-md-12 col-sm-12 col-12">

                    <div class="table-responsive">
                    <table class="table ceny__table">

                    <thead>
                        <tr>
                            <td>Фото</td>
                            <td>Яхта</td>
                            <td>Цена</td>
                        </tr>
                    </thead>

                    <?php
                    // -------------
                    // Start to loop
                    $prefix = 'yachts-adler';
                    $args = array( 'post_type' => $prefix, 'posts_per_page' => 200 );
                    $loop = new WP_Query( $args );
                    while ( $loop->have_posts() ) : $loop->the_post();
                    ?>
                    <?php
                    // ----------------------------
                    // Get thumbnail image alt text
                    $thumbId  = get_post_thumbnail_id($post->ID);
                    $thumbAlt = get_post_meta($thumbId, '_wp_attachment_image_alt', true);
                    $status = rwmb_meta($prefix . '-yachts_status');
                    
                    if($status == '1') {
                    ?>

                    <tr>
                        <td colspan="3">
                            <a href="<?= the_permalink() ?>" title="Яхта <?= the_title() ?> цена">
                                <div>
                                    <img loading="lazy" src="<?= get_the_post_thumbnail_url($post->ID, 'medium') ?>" alt="<?= the_title() ?>" title="<?= the_title() ?>" class="img-fluid">
                                </div>
                                <div class="ceny__middle_name">
                                    <span class="ceny__name"><?= rwmb_meta($prefix . '-yacht_name') ?></span>
                                    <span class="ceny__nickname"><?= rwmb_meta($prefix . '-yacht_nickname') ?></span>
                                </div>
                                <div>
                                    <span class="ceny__price">
                                        <?= number_format(rwmb_meta($prefix . '-yacht_price'), 0, '', '.'); ?>
                                    </span>
                                    <span class="ceny__units">руб/час</span>
                                </div>
                            </a>
                        </td>
                    </tr>

                    <?php
                    }
                    endwhile;
                    ?>
                    </table>
                    </div>
                    
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
                        set_query_var('yaside_posttype', 'yachts');
                        get_template_part('parts/pages/catalog-yacht/yaside');
                    ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php get_footer() ?>