<?php
function sochi_yachts_slider_types_shortcode($atts) {
    // Attributes
    extract( shortcode_atts(
        array(
            'prefix' => '', // yachts, yachts-adler, yachts-lazar etc.
            'type' => '',   // Класс яхты: 1 - моторная, 2 - парусная, 3 - катамаран, 4 - теплоход, 5 - катер
            'minprice' => '',
            'maxprice' => '',
        ), $atts )
    );

    $args = array(
        'post_type' => $prefix,
        'post_status' => 'publish',
    );

    $my_query = null;
    $my_query = new WP_query($args);

    echo '<div data-block="cards">
            <div data-block="carousel">
                <header>Яхты в Сочи</header>';

    if($my_query->have_posts()):
        while($my_query->have_posts()) : $my_query->the_post();

        $yacht_type = rwmb_meta($prefix . '-yacht_class');
        $yacht_price = rwmb_meta($prefix . '-yacht_price');

        if($yacht_type == $type || $type == '0') {
            if($yacht_price < $maxprice && $yacht_price > $minprice) {
            echo '<div data-block="snippet"
                    data-title="' . get_the_title() . '"
                    data-img="' . get_the_post_thumbnail_url($post->ID, 'medium') . '"
                    data-url="' . get_the_permalink() . '">
                </div>';
            }
        }

        endwhile;
        wp_reset_postdata();

        echo '  </div>
            </div>';

    else :
    _e( 'Нет яхт' );
    endif;
}
add_shortcode('sochi_yachts_slider_types', 'sochi_yachts_slider_types_shortcode');
?>