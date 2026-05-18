<?php
function sochi_blog_slider_shortcode($atts) {
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

    echo '<div data-block="cards">
            <div data-block="carousel">
                <header>Блог</header>';

    if($my_query->have_posts()):
        while($my_query->have_posts()) : $my_query->the_post();

        echo '<div data-block="snippet"
                data-title="' . get_the_title() . '"
                data-img="' . get_the_post_thumbnail_url($post->ID, 'medium') . '"
                data-url="' . get_the_permalink() . '">
            </div>';

        endwhile;
        wp_reset_postdata();

        echo '  </div>
            </div>';

    else :
    _e( 'Статьи не найдены' );
    endif;
}
add_shortcode('sochi_blog_slider', 'sochi_blog_slider_shortcode');
?>