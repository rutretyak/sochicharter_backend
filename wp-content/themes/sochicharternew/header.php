<?php get_template_part('_config') ?>
<?php $wp_styles = wp_styles(); ?>

<!doctype html>
<html dir="ltr" <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="SochiCharter">
    <meta name="format-detection" content="telephone=no">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <link rel="icon" type="image/svg+xml" href="<?= get_home_url() ?>/favicon.svg">
    
    <?php $css_href = esc_url(get_template_directory_uri()) . "/css/index.min.css?ver=" . time() ?>
    <link rel="preload" href="<?= $css_href ?>" as="style" />
    <link rel="stylesheet" href="<?= $css_href ?>" />

    <?php wp_head() ?>
    <?php
        if(get_query_var('calculator') === true) {
            get_template_part('parts/blocks/calculator/yachts-data');
        }
    ?>
</head>

<body <?php body_class(); ?>>
<?php // get_template_part("/parts/loader") ?>

<div class="header__fixed">
    <header class="header">
        <div class="container">
            <div class="row header__row">
                
                <?php if(is_front_page()) { ?>
                    <div class="header__logo logo col-xl-2 col-lg-2 col-md-2 col-sm-4 col-4">
                        <?php get_template_part('svg/logo') ?>
                    </div>
                <?php } else { ?>
                    <a href="<?= get_home_url() ?>" class="header__logo logo col-xl-2 col-lg-2 col-md-2 col-sm-4 col-4" title="На главную">
                        <?php get_template_part('svg/logo') ?>
                    </a>
                <?php } ?>

                <div class="header__phone col-xl-3 col-lg-4 col-md-4">
                    <div class="header__tel_wrap">
                        <a class="header__tel" href="tel:<?= $GLOBALS['phone_href_alt'] ?>" title="Связаться с нами по телефону <?= $GLOBALS['phone_rich_alt'] ?>">
                            <span class="header__phone_icon">
                                <?php get_template_part('svg/iphone') ?>
                            </span>    
                            <span><?= $GLOBALS['phone_alt'] ?></span>
                        </a>
                    </div>
                </div>

                <div class="header__msgs col-xl-3 col-lg-2 col-md-2 col-sm-4 col-4">
                    <div class="header__messangers">
                        <a class="header__tel_add whatsapp" href="<?= $GLOBALS['wa_link'] ?>" title="Начать чат в WhatsApp">
                            <span><?php get_template_part('svg/whatsapp') ?></span>
                            <span>WhatsApp</span>
                        </a>
                        <a class="header__tel_add telegram" href="<?= $GLOBALS['telegram'] ?>" title="Начать чат в Telegram">
                            <span><?php get_template_part('svg/telegram') ?></span>
                            <span>Telegram</span>
                        </a>
                    </div>
                </div>

                <div data-type="header__city" class="header__city col-xl-2 col-lg-3 col-md-2 col-sm-12 col-12">
                    <button type="button" data-type="header__citybtn">
                        <?php get_template_part('svg/location') ?>
                        <?php if($GLOBALS['isSochi']) { ?>
                            <span>Сочи</span>
                        <?php } ?>
                        <?php if($GLOBALS['isAdler']) { ?>
                            <span>Адлер</span>
                        <?php } ?>
                        <?php if($GLOBALS['isLazarevskoe']) { ?>
                            <span>Лазаревское</span>
                        <?php } ?>
                    </button>
                    <ul>
                        <li>
                            <a href="<?= get_home_url() ?>/" title="Аренда яхт в Сочи">
                                <span>Сочи</span>
                                <?php 
                                    if($GLOBALS['isSochi']) {
                                        get_template_part('svg/valid');
                                    }
                                ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= get_home_url() ?>/adler/" title="Аренда яхт в Адлере">
                                <span>Адлер</span>
                                <?php 
                                    if($GLOBALS['isAdler']) {
                                        get_template_part('svg/valid');
                                    }
                                ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= get_home_url() ?>/lazarevskoe/" title="Аренда яхт в Лазаревском">
                                <span>Лазаревское</span>
                                <?php 
                                    if($GLOBALS['isLazarevskoe']) {
                                        get_template_part('svg/valid');
                                    }
                                ?>
                            </a>
                        </li>

                    </ul>
                </div>

                <div data-type="header__search" class="header__search col-xl-2 d-none d-xl-flex">
                    <?php get_template_part('parts/search-form') ?>
                </div>
                
                <div class="header__tools col-lg-1 col-md-2 col-sm-4 col-4 d-block d-xl-none">
                    <div class="header__hamburger">   
                        <button class="hamburger hamburger--slider" type="button" title="Menu" data-type="hamburger">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                        </button>
                    </div>
                    <div class="header__mobisearch">
                        <button type="button" data-type="header__btnSearch" title="Поиск по сайту"><?php get_template_part('svg/search') ?></button>
                    </div>
                </div>
                
            </div>
        </div>
    </header>

    <?php // Desktop ?>
    <nav class="nav nav_d d-none d-xl-block d-lg-block" data-type="nav">
        <div class="container">
            <div class="row">
                <?php get_template_part('parts/nav-content') ?>
            </div>
        </div>
    </nav>

    <?php // Mobile ?>
    <nav class="nav nav_m d-xl-none d-lg-none" data-type="mnav">
        <div class="container">
            <div class="row">
                <div class="nav__mbox">
                    <?php get_template_part('parts/nav-content') ?>
                </div>
            </div>
        </div>
    </nav>
</div>

<?php if(!is_front_page()) { ?>
<div class="breadcrumbs">
    <div class="container">
        <div class="row">
            <div class="breadcrumbs__block" typeof="BreadcrumbList" vocab="http://schema.org/">
                <?php if(function_exists('bcn_display')) {
                    bcn_display();
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>

