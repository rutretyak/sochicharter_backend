<?php
function sochi_feedback_form_shortcode() {
    echo '
    <form data-type="callback" 
        data-send-to="admin@яхты-в-сочи.рф" 
        data-agreement-company="Sochi Charter" 
        data-agreement-link="https://xn-----elcz3ardxy8a4b.xn--p1ai/privacy-policy/">
    </form>
    ';
}
add_shortcode('sochi_feedback_form', 'sochi_feedback_form_shortcode');

function sochi_map_shortcode() {
    echo '
        <iframe src="https://yandex.ru/map-widget/v1/-/C0f3ECi~"></iframe>
    ';
}
add_shortcode('sochi_map', 'sochi_map_shortcode');
?>