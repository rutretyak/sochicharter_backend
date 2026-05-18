<?php get_template_part('_config'); ?>

<!doctype html>
<!--[if IE 8 ]> <html dir="ltr" <?php language_attributes(); ?> class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]> <html dir="ltr" <?php language_attributes(); ?> class="no-js ie9"> <![endif]-->
<!--[if gte IE 10]><!-->
<html dir="ltr" <?php language_attributes(); ?> class="no-js">
<!--<![endif]-->
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="author" content="SochiCharter">
    <meta name="format-detection" content="telephone=no">
	
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo esc_url( get_template_directory_uri() ); ?>/fav/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo esc_url( get_template_directory_uri() ); ?>/fav/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo esc_url( get_template_directory_uri() ); ?>/fav/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo esc_url( get_template_directory_uri() ); ?>/fav/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo esc_url( get_template_directory_uri() ); ?>/fav/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo esc_url( get_template_directory_uri() ); ?>/fav/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo esc_url( get_template_directory_uri() ); ?>/fav/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo esc_url( get_template_directory_uri() ); ?>/fav/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( get_template_directory_uri() ); ?>/fav/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="<?php echo esc_url( get_template_directory_uri() ); ?>/fav/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( get_template_directory_uri() ); ?>/fav/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo esc_url( get_template_directory_uri() ); ?>/fav/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url( get_template_directory_uri() ); ?>/fav/favicon-16x16.png">
    <link rel="manifest" href="<?php echo esc_url( get_template_directory_uri() ); ?>/fav/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?php echo esc_url( get_template_directory_uri() ); ?>/fav/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <link href="<?php echo esc_url( get_template_directory_uri() ); ?>/css/index.min.css" rel="stylesheet">

	<!--[if gte IE 9]>
	<style type="text/css">
		.gradient {
			filter: none;
		}
	</style>
	<![endif]-->
    
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<!--[if lt IE 9]>
<style>
.ie8 ~ * {
	display:none;
}
</style>
<center>
<big>
<div class="ie8">Вы используете старую версию браузера Internet Explorer
Пожалуйста, <a href="http://browsehappy.com/" title="Скачать браузер">скачайте современный браузер</a>
</div>
<big>
</center>
<![endif]-->

<header class="header">
<div class="container">
<div class="row">
    
    <div class="header__logo logo col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
        <?php get_template_part('svg/logo') ?>
    </div>

    <div data-type="header__search" class="header__search header__booking col-xl-6 col-lg-6 col-md-5 d-none col-md-5 d-xl-flex d-lg-flex d-md-flex">
        <span class="header__bookinfo">Работаем более 5 лет</span>
    </div>
    
    <div class="header__phone col-xl-3 col-lg-3 col-md-4 col-sm-9 col-12">
        <a href="tel:<?= $GLOBALS['phone_href'] ?>" title="Связаться с нами по телефону <?= $GLOBALS['phone_rich'] ?>">
            <?php get_template_part('svg/phone') ?>
            <span><?= $GLOBALS['phone'] ?></span>
        </a>
    </div>
    
</div>
</div>
</header>

