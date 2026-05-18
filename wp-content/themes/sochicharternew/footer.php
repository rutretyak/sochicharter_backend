<?php get_template_part('parts/footer/footer-content') ?>

<script src="<?= esc_url(get_template_directory_uri()) ?>/js/jquery.min.js"></script>
<script src="<?= esc_url(get_template_directory_uri()) ?>/js/slick.min.js"></script>
<script src="<?= esc_url(get_template_directory_uri()) ?>/js/jquery.inputmask.min.js"></script>
<script src="<?= esc_url(get_template_directory_uri()) ?>/js/inputmask.min.js?ver=<?= time() ?>"></script>
<script src="<?= plugin_dir_url('/') ?>lazy-load-for-comments/public/js/llc_scroll.min.js" id="lazy-load-for-comments-js"></script>
<script src="<?= esc_url(get_template_directory_uri()) ?>/js/index.min.js?ver=<?= time() ?>"></script>
<script src="//code.jivo.ru/widget/mZMyIEZq0r" async></script>
<?php get_template_part('parts/footer/footer-metrics') ?>
<?php wp_footer() ?>

</body>
</html>
