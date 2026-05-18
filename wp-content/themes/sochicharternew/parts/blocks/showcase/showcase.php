<div class="showcase">
    <div class="container">

        <div class="row">

            <button type="button" class="showcase__filterbtn" title="Фильтровать яхты" data-type="showcase__filterbtn">
                <?php get_template_part('svg/filters') ?>
                <span>Фильтры</span>
            </button>

            <a href="<?= get_home_url() ?>/calculator/" class="showcase__filterbtn showcase__calcbtn" title="Калькулятор мероприятий">
                <?php get_template_part('svg/calculator/calculator') ?>
                <span>Калькулятор</span>
            </a>

        </div>

        <div class="showcase__filters row">

            <form class="showcase__form" novalidate>
                <div class="row no-gutters">

                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-4 col-6">
                        <label for="sf_pricefrom" class="col">Цена от:</label>
                        
                        <div class="sf__field">
                            <?php get_template_part('svg/rub') ?>
                            <input type="number" class="col" id="sf_pricefrom" name="sf_pricefrom" title="Минимальная цена" placeholder="От" />
                        </div>

                        <span class="sf__error sf__error_pricefrom"></span>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-4 col-6">
                        <label for="sf_priceto" class="col">Цена до:</label>
                        
                        <div class="sf__field">
                            <?php get_template_part('svg/rub') ?>
                            <input type="number" class="col" id="sf_priceto" name="sf_priceto" title="Максимальная цена" placeholder="До" />
                        </div>

                        <span class="sf__error sf__error_priceto"></span>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-4 col-6">
                        <label for="sf_capacity" class="col">Гостей</label>

                        <div class="sf__field sf__field_capacity">
                            <?php get_template_part('svg/capacity') ?>
                            <input type="number" class="col" id="sf_capacity" name="sf_capacity" title="Пассажировместимость яхты" placeholder="Пассажиров" />
                        </div>  

                        <span class="sf__error sf__error_capacity"></span>
                    </div>
                    
                    <div class="w-100 d-xl-none d-lg-none d-md-block d-sm-block d-none"></div>

                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-4 col-6">
                        <label for="sf_cabins" class="col">Кают</label>

                        <div class="sf__field sf__field_cabins">
                            <?php get_template_part('svg/cabins') ?>
                            <input type="number" class="col" id="sf_cabins" name="sf_cabins" title="Количество кают в яхте" placeholder="Кают" />
                        </div>

                        <span class="sf__error sf__error_cabins"></span>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-8 col-12">
                        <label for="sf_type" class="col">Тип яхты</label>

                        <div class="sf__field sf__field_dropdown">
                            <?php get_template_part('svg/dropdown') ?>
                            <select class="col" id="sf_type" name="sf_type">
                                <option value="0">Все яхты</option>
                                <option value="1">Моторная</option>
                                <option value="2">Парусная</option>
                                <option value="3">Катамаран</option>
                                <option value="4">Теплоход</option>
                                <option value="5">Катер</option>
                            </select>
                        </div>
                    </div>

                    <div class="w-100 d-xl-none d-lg-none d-md-none d-sm-block d-none"></div>

                    <div class="showcase__buttons col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <button type="reset" class="showcase__resetbtn" title="Сбросить фильтры">
                            <?php get_template_part('svg/refresh') ?>
                        </button>
                        <button type="submit" class="showcase__applybtn" title="Применить фильтры">
                            <?php get_template_part('svg/right') ?>
                        </button>    
                    </div>
                </div>

                <input type="hidden" name="c_pricefrom" value="<?= get_query_var('c_pricefrom') ?>">
                <input type="hidden" name="c_priceto" value="<?= get_query_var('c_priceto') ?>">
                <input type="hidden" name="c_capacity" value="<?= get_query_var('c_capacity') ?>">
                <input type="hidden" name="c_cabins" value="<?= get_query_var('c_cabins') ?>">
                <input type="hidden" name="c_type" value="<?= get_query_var('c_type') ?>">

            </form>
            <div class="showcase__footer row no-gutters">

                <div class="showcase__sortprice col-xl-6 col-lg-6 col-md-8 col-sm-9 col-12 row no-gutters">

                    <label class="col-xl-3 col-lg-4 col-md-4 col-sm-5 col-12 d-xl-flex d-lg-flex d-md-flex d-sm-flex d-none" for="sf_sortprice">Сортировать:</label>

                    <div class="sf__field sf__field_dropdown col-xl-4 col-lg-5 col-md-5 col-sm-7 col-12">
                        <?php get_template_part('svg/dropdown') ?>
                        <select class="col" id="sf_sortprice" name="sf_sortprice">
                            <optgroup label="По новизне">
                                <option value="desc_date">Сначала новые</option>
                                <option value="asc_date">Сначала старые</option>
                            </optgroup>
                            <optgroup label="По цене">
                            <option value="asc_price">Сначала дешевле</option>
                            <option value="desc_price">Сначала дороже</option>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <div class="showcase__finded col-xl-6 col-lg-6 col-md-4 col-sm-3 col-12">
                    <span>найдено: <span class="sf__count"></span> <span class="sf__word"></span></span>
                </div>

            </div>
        </div>


        <?php
if(get_query_var('showcase_tags')) {
?>
<div class="showcase__tags row">
<ul>
    <li><a href="<?= get_home_url() ?>/adler/" title="Прокат яхт в Адлере">Адлер</a></li>
    <li><a href="<?= get_home_url() ?>/lazarevskoe/" title="Прокат яхт в Лазаревском">Лазаревское</a></li>
    <li><a href="<?= get_home_url() ?>/catalog-yacht/arenda-motornoy-yachti/" title="Аренда моторных яхт">Моторные</a></li>
    <li><a href="<?= get_home_url() ?>/catalog-yacht/arenda-parusnoy-yachti/" title="Аренда парусных">Парусные</a></li>
    <li><a href="<?= get_home_url() ?>/catalog-yacht/arenda-katamarana/" title="Аренда катамаранов">Катамараны</a></li>
    <li><a href="<?= get_home_url() ?>/catalog-yacht/arenda-teplohoda/" title="Аренда теплоходов">Теплоходы</a></li>
    <li><a href="<?= get_home_url() ?>/catalog-yacht/arenda-katera/" title="Аренда катеров">Катера</a></li>
    <li><a href="<?= get_home_url() ?>/ceny/" title="Цена аренды">Цены</a></li>
    <li><a href="<?= get_home_url() ?>/catalog-yacht/arenda-business-yachti/" title="Бизнес яхты">Бизнес</a></li>
    <li><a href="<?= get_home_url() ?>/catalog-yacht/arenda-vip-luxury-yachti/" title="VIP яхты">VIP</a></li>
    <li><a href="<?= get_home_url() ?>/catalog-yacht/arenda-vip-luxury-yachti/" title="Luxury яхты">Luxury</a></li>
    <li><a href="<?= get_home_url() ?>/catalog-yacht/arenda-vip-luxury-yachti/" title="Элитные яхты">Элитные</a></li>
    <li><a href="<?= get_home_url() ?>/catalog-yacht/arenda-vip-luxury-yachti/" title="Дорогие яхты">Дорогие</a></li>
    <li><a href="<?= get_home_url() ?>/catalog-yacht/arenda-econom-yachti/" title="Недорогие яхты">Недорогие</a></li>
    <li><a href="<?= get_home_url() ?>/catalog-yacht/arenda-vip-luxury-yachti/" title="Большие яхты">Большие</a></li>
    <li><a href="<?= get_home_url() ?>/ceny/" title="Почасовая аренда">Почасовая</a></li>
    <li><a href="<?= get_home_url() ?>/ceny/" title="Посуточная аренда">Посуточная</a></li>
    <li><a href="<?= get_home_url() ?>/adler/" title="Яхты в Иммеретинском">Иммеретинский</a></li>
    <li><a href="<?= get_home_url() ?>/adler/" title="Яхты в Олимпийском парке">Олимпийский парк</a></li>
    <li><a href="<?= get_home_url() ?>/uslugi/uzhin-na-yachte/" title="Ужин на яхте">Ужин</a></li>
    <li><a href="<?= get_home_url() ?>/uslugi/predlozhenie-na-yachte/" title="Предложение на яхте">Предложение</a></li>
    <li><a href="<?= get_home_url() ?>/uslugi/rybalka-v-sochi/" title="Рыбалка на яхте">Морская рыбалка</a></li>
    <li><a href="<?= get_home_url() ?>/uslugi/den-rozdeniya/" title="День рождения на яхте">День рождения</a></li>
    <li><a href="<?= get_home_url() ?>/uslugi/svadba-na-yahte-v-sochi/" title="Свадьба на яхте">Свадьба</a></li>
    <li><a href="<?= get_home_url() ?>/uslugi/foto-i-video-na-yachte/" title="Фотосессия на яхте">Фото</a></li>
    <li><a href="<?= get_home_url() ?>/uslugi/foto-i-video-na-yachte/" title="Видеосъёмка на яхте">Видео</a></li>
    <li><a href="<?= get_home_url() ?>/uslugi/uzhin-na-yachte/" title="Свидание на яхте">Свидание</a></li>
</ul>
</div>
<?php
}
?>

<div class="showcase__viewbox row collapsed">
<?php
    // -------------
    // Start to loop
    $prefix = get_query_var('showcase_posttype');
    $args = array( 'post_type' => get_query_var('showcase_posttype'), 'posts_per_page' => 200 );
    $loop = new WP_Query( $args );
    while ( $loop->have_posts() ) : $loop->the_post();
    $ystatus = rwmb_meta($prefix . '-yachts_status') == 1 ? 'ycard_available' : 'ycard_unavailable';
?>
<?php
$ycard_city = '';
switch($prefix) {
    case 'yachts': $ycard_city = 'Сочи'; break;
    case 'yachts-adler': $ycard_city = 'Адлере'; break;
    case 'yachts-lazar': $ycard_city = 'Лазаревском'; break;
    default: $ycard_city = 'Сочи'; break;
}
?>
<?php
$ycard_type = '';
$ycard_single_type = '';
switch(rwmb_meta($prefix . '-yacht_class')) {
    case '1': $ycard_type = 'моторной яхты'; $ycard_single_type = 'Яхта'; break;
    case '2': $ycard_type = 'парусной яхты'; $ycard_single_type = 'Яхта'; break;
    case '3': $ycard_type = 'катамарана'; $ycard_single_type = 'Катамаран'; break;
    case '4': $ycard_type = 'теплохода'; $ycard_single_type = 'Теплоход'; break;
    case '5': $ycard_type = 'катера'; $ycard_single_type = 'Катер'; break;
    default: break;
}
?>

<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6 ycard">
    <a class="<?= $ystatus ?>" href="<?= the_permalink() ?>" title="Аренда <?= $ycard_type ?> <?= the_title() ?> в <?= $ycard_city ?>">
        
        <input type="hidden" value="<?= rwmb_meta($prefix . '-yacht_price') ?>" data-type="sf_price">
        <input type="hidden" value="<?= rwmb_meta($prefix . '-yacht_capacity') ?>" data-type="sf_capacity">
        <input type="hidden" value="<?= rwmb_meta($prefix . '-yacht_class') ?>" data-type="sf_type">
        <input type="hidden" value="<?= rwmb_meta($prefix . '-yacht_cabins') ?>" data-type="sf_cabins">
        <input type="hidden" value="<?= rwmb_meta($prefix . '-yacht_name') ?>" data-type="sf_name">
        <input type="hidden" value="<?= get_the_ID() ?>" data-type="sf_id">
        <input type="hidden" value="<?= get_the_date('Y/m/d') ?>" data-type="sf_date">

        <div class="ycard__block">
        
            <div class="ycard__iwrap d-flex">
                <img src="<?php get_template_part('svg/loading') ?>" data-type="lazyload-img" data-img="<?= get_the_post_thumbnail_url($post->ID, 'medium') ?>" alt="<?= $ycard_single_type ?> <?= the_title() ?> в <?= $ycard_city ?>" class="img-fluid">
                <noscript>
                    <img src="<?= get_the_post_thumbnail_url($post->ID, 'medium') ?>" alt="<?= $ycard_single_type ?> <?= the_title() ?> в <?= $ycard_city ?>" class="img-fluid">
                </noscript>
            </div>

            <?php 
                $ycard_class = rwmb_meta($prefix . '-yacht_class');
                $ycard_caption = '';
                switch($ycard_class) {
                    case 1: $ycard_caption = 'Моторная яхта'; break;
                    case 2: $ycard_caption = 'Парусная яхта'; break;
                    case 3: $ycard_caption = 'Катамаран'; break;
                    case 4: $ycard_caption = 'Теплоход'; break;
                    case 5: $ycard_caption = 'Катер'; break;
                }
            ?>

            <span class="ycard__caption w-100 d-block"><?= $ycard_caption ?></span>
            <span class="ycard__title w-100 d-block"><?= rwmb_meta($prefix . '-yacht_name') ?></span>
            
            <div class="ycard__rent">
                <span>Аренда</span>
            </div>

            <div class="ycard__info d-flex justify-content-between">

                <div class="ycard__infol">
                    <?php //get_template_part('svg/price') ?>
                    <div class="ycard__pricebg"></div>
                    <span class="ycard__price"><?= rwmb_meta($prefix . '-yacht_price') ?></span>
                    <span class="ycard__pricetxt">руб/час</span>
                </div>

                <div class="ycard__infor">
                    <?php //get_template_part('svg/capacity') ?>
                    <div class="ycard__capacitybg"></div>
                    <span class="ycard__capacity"><?= rwmb_meta($prefix . '-yacht_capacity') ?></span>
                    <span class="ycard__capacitytxt">чел</span>
                </div>

            </div>

        </div>
    </a>
</div>

<?php
    endwhile;
?>
</div>

<div class="showcase__showmore">
    <button class="show" type="button" title="Показать все яхты и катера" data-type="showcase__showmore">
        <span>ВСЕ ЯХТЫ</span>
        <?php get_template_part('svg/right') ?>
    </button>
</div>

    </div>
</div>