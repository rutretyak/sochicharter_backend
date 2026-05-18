<?php get_template_part('parts/footer/footer-content') ?>

<link href="<?= esc_url(get_template_directory_uri()) ?>/css/index-city.min.css" rel="stylesheet">

<script src="<?= esc_url(get_template_directory_uri()) ?>/js/jquery.min.js"></script>
<?php /*
<script src="<?= esc_url(get_template_directory_uri()) ?>/js/slick.min.js"></script>
*/ ?>
<script src="<?= esc_url(get_template_directory_uri()) ?>/js/jquery.inputmask.min.js"></script>
<script src="<?= esc_url(get_template_directory_uri()) ?>/js/inputmask.min.js"></script>
<script src="<?= esc_url(get_template_directory_uri()) ?>/js/index-city.min.js"></script>



<?php /*
<script src="//code.jivosite.com/widget/4QWpfrufdH" async></script>
<script src="<?= esc_url(get_template_directory_uri()) ?>/js/cron/jivosite.js" async></script>
<script src="<?= esc_url(get_template_directory_uri()) ?>/js/addthis_widget.js#pubid=ra-5cc18b24fd294f15"></script> 
*/ ?>

<?php get_template_part('parts/footer/footer-metrics') ?>


<?php /*
<!-- Begin Talk-Me {literal} -->
<script type='text/javascript'>
	(function(d, w, m) {
		window.supportAPIMethod = m;
		var s = d.createElement('script');
		s.type ='text/javascript'; s.id = 'supportScript'; s.charset = 'utf-8';
		s.async = true;
		var id = 'a1bc67682f1cdd960fc0684a673816aa';
		s.src = 'https://lcab.talk-me.ru/support/support.js?h='+id;
		var sc = d.getElementsByTagName('script')[0];
		w[m] = w[m] || function() { (w[m].q = w[m].q || []).push(arguments); };
		if (sc) sc.parentNode.insertBefore(s, sc); 
		else d.documentElement.firstChild.appendChild(s);
	})(document, window, 'TalkMe');
</script>
<!-- {/literal} End Talk-Me -->
*/ ?>

<?php wp_footer() ?>

</body>
</html>
