<?php
function sochi_yachts_ceny_shortcode($atts) {
    // Attributes
    extract( shortcode_atts(
        array(
            'prefix' => '',
        ), $atts )
    );

    $args = array(
        'post_type' => $prefix,
        'post_status' => 'publish',
    );

    $my_query = null;
    $my_query = new WP_query($args);

    echo '
    <table>
    <thead>
        <tr>
            <td>Фото</td>
            <td>Яхта</td>
            <td>руб/час</td>
        </tr>
    </thead>
    <tbody>
    ';

    if($my_query->have_posts()):
        while($my_query->have_posts()) : $my_query->the_post();

            echo '
            <tr>
                <td><img src="' . get_the_post_thumbnail_url($post->ID, 'medium') . '"></td>
                <td><a href="' . get_the_permalink() . '" data-turbo="true">' . get_the_title() . '</a></td>
                <td>' . rwmb_meta($prefix . '-yacht_price') . '</td>
            </tr>
            ';

        endwhile;
        wp_reset_postdata();

        echo '
        </tbody>
        </table>
        ';

    else :
    _e( 'Нет яхт' );
    endif;
}

add_shortcode('sochi_yachts_ceny', 'sochi_yachts_ceny_shortcode');

function sochi_yachts_ceny_day_shortcode($atts) {
    // Attributes
    extract( shortcode_atts(
        array(
            'prefix' => '',
        ), $atts )
    );

    $args = array(
        'post_type' => $prefix,
        'post_status' => 'publish',
    );

    $my_query = null;
    $my_query = new WP_query($args);

    echo '
    <table>
    <thead>
        <tr>
            <td>Фото</td>
            <td>Яхта</td>
            <td>руб/день</td>
        </tr>
    </thead>
    <tbody>
    ';

    if($my_query->have_posts()):
        while($my_query->have_posts()) : $my_query->the_post();

            echo '
            <tr>
                <td><img src="' . get_the_post_thumbnail_url($post->ID, 'medium') . '"></td>
                <td><a href="' . get_the_permalink() . '" data-turbo="true">' . get_the_title() . '</a></td>
                <td>' . rwmb_meta($prefix . '-yacht_priceday') . '</td>
            </tr>
            ';

        endwhile;
        wp_reset_postdata();

        echo '
        </tbody>
        </table>
        ';

    else :
    _e( 'Нет яхт' );
    endif;
}

add_shortcode('sochi_yachts_ceny_day', 'sochi_yachts_ceny_day_shortcode');

function sochi_yachts_ceny_week_shortcode($atts) {
    // Attributes
    extract( shortcode_atts(
        array(
            'prefix' => '',
        ), $atts )
    );

    $args = array(
        'post_type' => $prefix,
        'post_status' => 'publish',
    );

    $my_query = null;
    $my_query = new WP_query($args);

    echo '
    <table>
    <thead>
        <tr>
            <td>Фото</td>
            <td>Яхта</td>
            <td>руб/неделя</td>
        </tr>
    </thead>
    <tbody>
    ';

    if($my_query->have_posts()):
        while($my_query->have_posts()) : $my_query->the_post();

            echo '
            <tr>
                <td><img src="' . get_the_post_thumbnail_url($post->ID, 'medium') . '"></td>
                <td><a href="' . get_the_permalink() . '" data-turbo="true">' . get_the_title() . '</a></td>
                <td>' . rwmb_meta($prefix . '-yacht_pricedayweek') . '</td>
            </tr>
            ';

        endwhile;
        wp_reset_postdata();

        echo '
        </tbody>
        </table>
        ';

    else :
    _e( 'Нет яхт' );
    endif;
}

add_shortcode('sochi_yachts_ceny_week', 'sochi_yachts_ceny_week_shortcode');
?>