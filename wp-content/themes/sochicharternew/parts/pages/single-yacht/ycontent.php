<?php
/*
Use https://docs.metabox.io/fields/image-advanced/ - for image gallery
Use https://docs.metabox.io/displaying-fields/ - for default fields
*/

$prefix = get_query_var('sy_posttype');
$ystatus = rwmb_meta($prefix . '-yachts_status') == 1 ? 'yacht__slider_available' : 'yacht__slider_unavailable';
$comments_count = wp_count_comments(get_the_ID());
?>

<div class="page page_inner page_yacht">

    <div class="yacht">
        <div class="container">
            <div class="row">

            <main class="col-lg-8">

                <div class="yacht__header w-100 d-flex flex-row justify-content-between">
                    <h1 class="yacht__title"><?= rwmb_meta($prefix . '-yacht_name') ?></h1>
                    <div class="yacht__price d-flex flex-row">
                        <span class="yacht__priceIcon price-bg"></span>
                        <span class="yacht__priceAmount"><?= rwmb_meta($prefix . '-yacht_price') ?></span>
                        <span class="yacht__priceText">руб/час</span>
                    </div>
                </div>

                <?php // Yacht slider ?>
                <div class="yacht__slider col <?= $ystatus ?>">
                <div class="yacht__list"
                data-type="slick"
                data-lazyload="ondemand"
                data-speed="900"
                data-infinite-md="true"
                data-infinite-sm="true"
                data-infinite-xs="true"
                data-stshow-md="1"
                data-stshow-sm="1"
                data-stshow-xs="1"
                data-stscroll-md="1"
                data-stscroll-sm="1"
                data-stscroll-xs="1"
                data-dots-md="true"
                data-dots-sm="true"
                data-dots-xs="false"
                data-adaptiveheight="true">

                <?php
                $images = rwmb_meta($prefix . '-yachts_images', array('size' => 'full'));
                foreach ($images as $image) {
                ?>
                    <div>
                        <img class="yacht__img img-fluid" src="<?= $image['url'] ?>" alt="<?= rwmb_meta($prefix . '-yacht_name') ?>">
                    </div>
                
                <?php } ?>
                </div>
                </div>
                <?php // #Yacht slider ?>

                <div class="tabs" data-type="tabs">
                    <ul class="tabs__captions">
                        <li class="active">
                            <?php get_template_part('svg/single-yacht/settings-cogwheel-button') ?>
                            <span>Характеристики</span>
                        </li>
                        <li>
                            <?php get_template_part('svg/menu/book') ?>
                            <span>Описание</span>
                        </li>
                        <li data-type="scrollCommentsOnClick">
                            <?php get_template_part('svg/menu/chat') ?>
                            <span>Отзывы (<?= $comments_count->approved ?>)</span>
                        </li>
                    </ul>
                    <div class="tabs__content">
                        <div class="tabs__view active">
                            <div class="table-responsive">
                                <table class="table yacht__table icons">
                                <tbody>
                                    <tr>
                                        <th>
                                            <?php get_template_part('svg/single-yacht/anchor') ?>
                                            <span>Модель</span>
                                        </th>
                                        <td><?= rwmb_meta($prefix . '-yacht_name') ?></td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php get_template_part('svg/single-yacht/name') ?>
                                            <span>Название</span>
                                        </th>
                                        <td><?= rwmb_meta($prefix . '-yacht_nickname') ?></td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php 
                                                $ycls = rwmb_meta($prefix . '-yacht_class');
                                                $icon = '';
                                                switch($ycls) {
                                                    case '1': $icon = 'motornye'; break;
                                                    case '2': $icon = 'parusnye'; break;
                                                    case '3': $icon = 'katamarany'; break;
                                                    case '4': $icon = 'teplohod'; break;
                                                    default:  $icon = 'katera'; break;
                                                }
                                                get_template_part('svg/catalog-yaht/' . $icon) 
                                            ?>
                                            <span>Класс</span>
                                        </th>
                                        <td>
                                        <?php 
                                            $cls = rwmb_meta($prefix . '-yacht_class');
                                            $result = '';
                                            switch($cls) {
                                                case '1': $result = 'Моторная'; break;
                                                case '2': $result = 'Парусная'; break;
                                                case '3': $result = 'Катамаран'; break;
                                                case '4': $result = 'Теплоход'; break;
                                                default:  $result = 'Нет класса'; break;
                                            }
                                            echo $result;
                                        ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php get_template_part('svg/single-yacht/mozy') ?>
                                            <span>Производитель</span>
                                        </th>
                                        <td><?= rwmb_meta($prefix . '-yacht_manufacturer') ?></td>
                                    </tr>
                                    <tr>
                                    <th>
                                        <?php get_template_part('svg/menu/yacht') ?>
                                        <span>Верфь</span>
                                    </th>
                                        <?php
                                            $shipyard = rwmb_meta($prefix . '-yacht_shipyard');                            
                                            $shipyard_title = '';
                                            $shipyard_link = '';
                                            switch($shipyard) {
                                                case 1: $shipyard_title = 'Majesty Yachts (ОАЭ)'; $shipyard_link = 'https://majesty.gulfcraftinc.com/'; break;
                                                case 2: $shipyard_title = 'Azimut Yachts (Италия)'; $shipyard_link = 'https://www.azimutyachts.com/'; break;
                                                case 3: $shipyard_title = 'Beneteau (Франция)'; $shipyard_link = 'https://www.beneteau.com/'; break;
                                                case 4: $shipyard_title = 'Bavaria (Германия)'; $shipyard_link = 'https://www.bavariayachts.com/'; break;
                                                case 5: $shipyard_title = 'Bayliner (США)'; $shipyard_link = 'https://bayliner.com/'; break;
                                                case 6: $shipyard_title = 'Carver (США)'; $shipyard_link = 'https://www.carveryachts.com/'; break;
                                                case 7: $shipyard_title = 'Chaparral (США)'; $shipyard_link = 'http://www.chaparralboats.com/'; break;
                                                case 8: $shipyard_title = 'Concept (США)'; $shipyard_link = 'https://conceptboats.com/'; break;
                                                case 9: $shipyard_title = 'Cranchi (Италия)'; $shipyard_link = 'https://www.cranchi.com/'; break;
                                                case 10: $shipyard_title = 'Dufour Yachts (Франция)'; $shipyard_link = 'https://www.dufour-yachts.com/'; break;
                                                case 11: $shipyard_title = 'Fairline (Великобритания)'; $shipyard_link = 'https://www.fairline.com/'; break;
                                                case 12: $shipyard_title = 'Ferretti (Италия)'; $shipyard_link = 'https://www.ferretti-yachts.com/en-us/'; break;
                                                case 13: $shipyard_title = 'Four Winns (США)'; $shipyard_link = 'https://www.fourwinns.com/us'; break;
                                                case 14: $shipyard_title = 'Lagoon (Франция)'; $shipyard_link = 'https://www.cata-lagoon.com/ru'; break;
                                                case 15: $shipyard_title = 'Fountaine Pajot (Франция)'; $shipyard_link = 'https://www.catamarans-fountaine-pajot.com/en/'; break;
                                                case 16: $shipyard_title = 'Linssen (Нидерланды)'; $shipyard_link = 'https://www.linssenyachts.com/en/'; break;
                                                case 17: $shipyard_title = 'Maxum Boat (США)'; $shipyard_link = 'https://global.bayliner.com/'; break;
                                                case 18: $shipyard_title = 'Meridian (США)'; $shipyard_link = 'https://www.brunswick.com/'; break;
                                                case 19: $shipyard_title = 'Monterey (США)'; $shipyard_link = 'https://www.montereyboats.com/'; break;
                                                case 20: $shipyard_title = 'Prestige (Франция)'; $shipyard_link = 'https://www.prestige-yachts.com/'; break;
                                                case 21: $shipyard_title = 'Princess Yachts (Великобритания)'; $shipyard_link = 'https://ru.princessyachts.com/'; break;
                                                case 22: $shipyard_title = 'Sea Ray (США)'; $shipyard_link = 'https://www.searay.eu/eu/en.html'; break;
                                                case 23: $shipyard_title = 'Hanse Yachts (Германия)'; $shipyard_link = 'https://www.hanseyachtsag.com/us/sealine.html'; break;
                                                case 24: $shipyard_title = 'Silverton Marine (США)'; $shipyard_link = 'https://www.silverton.com/'; break;
                                                case 25: $shipyard_title = 'Starfisher (Португалия)'; $shipyard_link = 'http://www.starfisher.com/'; break;
                                                case 26: $shipyard_title = 'Sunseeker (Великобритания)'; $shipyard_link = 'https://www.sunseeker.com/en-GB/'; break;
                                                case 27: $shipyard_title = 'Velvette Marine (Россия)'; $shipyard_link = 'https://velvette-marine.com/'; break;
                                                case 28: $shipyard_title = 'Wellcraft (США)'; $shipyard_link = 'https://www.wellcraft.com/'; break;
                                                default: $shipyard_link = ''; $shipyard_title = ''; break;
                                            }
                                        ?>
                                        <td>
                                            <?php if($shipyard_title === '') { ?>
                                            -
                                            <?php } else { ?>
                                            <a href="<?= $shipyard_link ?>" title="<?= $shipyard_title ?>"><?= $shipyard_title ?></a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php get_template_part('svg/single-yacht/calendar-small-page') ?>
                                            <span>Год постройки</span>
                                        </th>
                                        <td><?= rwmb_meta($prefix . '-yacht_year') ?></td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php get_template_part('svg/single-yacht/engine') ?>
                                            <span>Двигатель</span>
                                        </th>
                                        <td><?= rwmb_meta($prefix . '-yacht_motor') ?></td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php get_template_part('svg/single-yacht/boat-length') ?>
                                            <span>Длина</span>
                                        </th>
                                        <td><?= rwmb_meta($prefix . '-yacht_length') ?> (м)</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php get_template_part('svg/single-yacht/expand-width') ?>
                                            <span>Ширина</span>
                                        </th>
                                        <td><?= rwmb_meta($prefix . '-yacht_wide') ?> (м)</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php get_template_part('svg/single-yacht/dimension-of-line-height') ?>
                                            <span>Осадка</span>
                                        </th>
                                        <td><?= rwmb_meta($prefix . '-yacht_draft') ?> (м)</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php get_template_part('svg/single-yacht/stopwatch') ?>
                                            <span>Скорость</span>
                                        </th>
                                        <td><?= rwmb_meta($prefix . '-yacht_speed') ?> (узлов)</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php get_template_part('svg/cabins') ?>
                                            <span>Количество кают</span>
                                        </th>
                                        <td><?= rwmb_meta($prefix . '-yacht_cabins') ?> (шт)</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php get_template_part('svg/capacity') ?>
                                            <span>Пассажировместимость</span>
                                        </th>
                                        <td><?= rwmb_meta($prefix . '-yacht_capacity') ?> (человек)</td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="tabs__view">
                            <div class="yacht__block w-100">
                                <span class="yacht__have w-100">В наличие</span>
                                <div class="yacht__stock w-100"><?= rwmb_meta($prefix . '-yacht_stock') ?></div>
                                <span class="yacht__have w-100">Обзор</span>
                                <div class="yacht__description w-100"><?= rwmb_meta($prefix . '-yacht_description') ?></div>
                            </div>
                        </div>

                        <div class="tabs__view">
                            <div class="yacht__block w-100">
                                <?php
                                    if ( comments_open() || get_comments_number() ) :
                                        comments_template();
                                    endif;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

            </main>

            <aside class="col-lg-4">

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

                <div class="yacht__similar">
                <h4>Похожие яхты</h4>
                
                <?php
                    $similar_yachts = rwmb_meta($prefix . '-yacht_similar');
                    
                    if(count($similar_yachts) == 0) {
                        // No similar yachts
                ?>
                        <span class="yacht__nosimilar">Нет похожих яхт</span>
                <?php
                    } else {
                ?>
                        <ul class="yacht__ulsimilar">
                <?php
                        // There is some similar yachts
                        for($x = 0; $x < count($similar_yachts); $x++) {
                            $id = $similar_yachts[$x];
                            $similar_yacht['name'] = rwmb_meta($prefix . '-yacht_name', '', $id);
                            $similar_yacht['price'] = rwmb_meta($prefix . '-yacht_price', '', $id);
                            $similar_yacht['class'] = rwmb_meta($prefix . '-yacht_class', '', $id);
                            $similar_yacht['thumbnail'] = get_the_post_thumbnail_url($id, 'thumbnail');
                            $similar_yacht['permalink'] = get_permalink($id);
                            $similar_yacht['title'] = rwmb_meta($prefix . '-yacht_title', '', $id);
                            $similar_yacht['status'] = rwmb_meta($prefix . '-yachts_status', '', $id) == 1 ? 'available' : 'unavailable';

                            switch($similar_yacht['class']) {
                                case '1': $similar_yacht['classStr'] = 'Моторная'; break;
                                case '2': $similar_yacht['classStr'] = 'Парусная'; break;
                                case '3': $similar_yacht['classStr'] = 'Катамаран'; break;
                                case '4': $similar_yacht['classStr'] = 'Теплоход'; break;
                                case '5': $similar_yacht['classStr'] = 'Катер'; break;
                            }
                ?>
                        <li>
                            <span class="yacht__sbstatus <?= $similar_yacht['status'] ?>">Недоступно</span>
                            <a href="<?= $similar_yacht['permalink'] ?>" title="Аренда яхты <?= $similar_yacht['name'] ?>">
                                <img src="<?= $similar_yacht['thumbnail'] ?>" alt="Прокат яхты <?= $similar_yacht['name'] ?>" />
                                <div class="yacht__similarblock">
                                    <span class="yacht__sbname"><?= $similar_yacht['name'] ?></span>
                                    <span class="yacht__sbprice">Цена: <em><?= $similar_yacht['price'] ?></em> руб/час</span>
                                    <span class="yacht__sbclass">Тип: <?= $similar_yacht['classStr'] ?></span>
                                </div>
                            </a>
                        </li>
                <?php
                        }
                ?>
                        </ul>
                <?php
                    }
                ?>
                </div>

                <?php 
                    set_query_var('yaside_posttype', $prefix);
                    get_template_part('parts/pages/catalog-yacht/yaside');
                ?>
            </aside>

            </div>
        </div>
    </div>

</div>