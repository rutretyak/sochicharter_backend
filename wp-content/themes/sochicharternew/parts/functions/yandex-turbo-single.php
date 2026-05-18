<?php
function sochi_single_yacht_slider_shortcode($atts) {
    // Attributes
    extract( shortcode_atts(
        array(
            'prefix' => '',
        ), $atts )
    );

    echo '<div data-block="slider" data-view="landscape">';

    $images = rwmb_meta($prefix . '-yachts_images', array('size' => 'full'));
    $name = rwmb_meta($prefix . '-yacht_name');
    foreach ($images as $image) {
    
        echo '
            <figure>
                <figcaption>' . $name . '</figcaption>
                <img src="' . $image['url'] . '"/>
            </figure>
        ';
    }

    echo '</div>';

}
add_shortcode('sochi_single_yacht_slider', 'sochi_single_yacht_slider_shortcode');

function sochi_single_yacht_table_shortcode($atts) {
    // Attributes
    extract( shortcode_atts(
        array(
            'prefix' => '',
        ), $atts )
    );

    // Get class
    $cls = rwmb_meta($prefix . '-yacht_class');
    $result = '';
    switch($cls) {
        case '1': $result = 'Моторная'; break;
        case '2': $result = 'Парусная'; break;
        case '3': $result = 'Катамаран'; break;
        case '4': $result = 'Теплоход'; break;
        default:  $result = 'Нет класса'; break;
    }
    $yacht_class = $result;
    // #Get class

    // Get shipyard
    $shipyard = rwmb_meta($prefix . '-yacht_shipyard');                            
    $shipyard_title = '';
    $shipyard_link = '';
    $yacht_shipyard = '-';
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
    if($shipyard_title != '') {
        $yacht_shipyard = '<a href="' . $shipyard_link . '" title="' . $shipyard_title . '">' . $shipyard_title . '</a>';
    }
    // #Get shipyard

    echo '
    <table>
        <tr>
            <td><big><b>Цена (руб/час)</b></big></td>
            <td><big><b>' . rwmb_meta($prefix . "-yacht_price") . '</b></big></td>
        </tr>
        <tr>
            <td>Вместимость (чел)</td>
            <td>' . rwmb_meta($prefix . "-yacht_capacity") . '</td>
        </tr>
        <tr>
            <td>Модель</td>
            <td>' . rwmb_meta($prefix . "-yacht_name") . '</td>
        </tr>
        <tr>
            <td>Название</td>
            <td>' . rwmb_meta($prefix . "-yacht_nickname") . '</td>
        </tr>
        <tr>
            <td>Класс</td>
            <td>' . $yacht_class . '</td>
        </tr>
        <tr>
            <td>Производитель</td>
            <td>' . rwmb_meta($prefix . "-yacht_manufacturer") . '</td>
        </tr>
        <tr>
            <td>Верфь</td>
            <td>' . $yacht_shipyard . '</td>
        </tr>
        <tr>
            <td>Год постройки</td>
            <td>' . rwmb_meta($prefix . "-yacht_year") . '</td>
        </tr>
        <tr>
            <td>Двигатель</td>
            <td>' . rwmb_meta($prefix . "-yacht_motor") . '</td>
        </tr>
        <tr>
            <td>Длина (м)</td>
            <td>' . rwmb_meta($prefix . "-yacht_length") . '</td>
        </tr>
        <tr>
            <td>Ширина (м)</td>
            <td>' . rwmb_meta($prefix . "-yacht_wide") . '</td>
        </tr>
        <tr>
            <td>Осадка (м)</td>
            <td>' . rwmb_meta($prefix . "-yacht_draft") . '</td>
        </tr>
        <tr>
            <td>Скорость (узлов)</td>
            <td>' . rwmb_meta($prefix . "-yacht_speed") . '</td>
        </tr>
        <tr>
            <td>Кают</td>
            <td>' . rwmb_meta($prefix . "-yacht_cabins") . '</td>
        </tr>
    </table>
    ';
}
add_shortcode('sochi_single_yacht_table', 'sochi_single_yacht_table_shortcode');

function sochi_single_yacht_stock_shortcode($atts) {
    // Attributes
    extract( shortcode_atts(
        array(
            'prefix' => '',
        ), $atts )
    );

    echo '
    <p><big><b>В наличии</b></big></p>
    ' . rwmb_meta($prefix . "-yacht_stock") . '
    ';
}
add_shortcode('sochi_single_yacht_stock', 'sochi_single_yacht_stock_shortcode');

function sochi_single_yacht_comments_shortcode() {

    $recent_comments = get_comments( array( 
        'post_id'   => get_the_ID(),
        'number'      => 40,            // number of comments to retrieve.
        'status'      => 'approve',     // we only want approved comments.
        'post_status' => 'publish'      // limit to published comments.
    ));
    $all_comments = get_comments(array(
        'post_id'   => get_the_ID(),
        'status'      => 'approve',
        'post_status' => 'publish'
    ));
    
    if(count($all_comments) > 0) {
        echo '
        <p><big><b>Отзывы</b></big></p>
        <div data-block="comments" data-url="' . get_the_permalink() . '">
        ';

        if ($recent_comments) {
            foreach((array) $recent_comments as $comment) {
                if($comment->comment_author != 'SKILINE') {
                    $avatar = get_avatar_url(get_comment_author_email($comment->ID), array(
                        'size' => 48,
                        'default'=>'monsterid',
                    ));
                    echo '
                    <div data-block="comment"
                    data-author="' . $comment->comment_author . '"
                    data-avatar-url="' . $avatar . '"
                    data-subtitle="' . $comment->comment_date . '"
                    >
                        <div data-block="content">
                            <p>' . $comment->comment_content . '</p>
                        </div>
                    </div>
                    ';
                }
            }
        }

        echo '
        </div>
        ';
    }
}
add_shortcode('sochi_single_yacht_comments', 'sochi_single_yacht_comments_shortcode');

?>
