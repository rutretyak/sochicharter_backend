<?php get_template_part('parts/footer/footer-content') ?>

<?php
    $cssHref = esc_url(get_template_directory_uri()) . "/css/index.min.css?ver=" . time();
?>

<noscript>
    <link href="<?= $cssHref ?>" rel="stylesheet" type="text/css">
</noscript>

<script>
(function() {
    var cssMain = document.createElement('link');
    cssMain.href = '<?= $cssHref ?>';
    cssMain.rel = 'stylesheet';
    cssMain.type = 'text/css';
    document.getElementsByTagName('head')[0].appendChild(cssMain);
})();
</script>

<script src="<?= esc_url(get_template_directory_uri()) ?>/js/jquery.min.js"></script>
<script src="<?= esc_url(get_template_directory_uri()) ?>/js/index.min.js?ver=<?= time() ?>"></script>
<script src="<?= esc_url(get_template_directory_uri()) ?>/js/ion.rangeSlider.min.js"></script>
<script src="<?= esc_url(get_template_directory_uri()) ?>/js/flatpickr.min.js"></script>
<script src="<?= esc_url(get_template_directory_uri()) ?>/js/flatpickr.ru.js"></script>
<script src="<?= esc_url(get_template_directory_uri()) ?>/js/index-calculator.min.js"></script>

<script src="//code.jivo.ru/widget/mZMyIEZq0r" async></script>
<?php get_template_part('parts/footer/footer-metrics') ?>
<?php wp_footer() ?>

</body>
</html>
