<ul class="nav__ul col">
    <li class="nav__sector">
        <a href="<?= get_home_url() ?>" class="a_yellowgreen" title="Главная">
            <span class="nav__titlewrap">
                <span class="nav__title">Главная</span>
            </span>
        </a>
    </li>
    <li class="nav__sector nav__nolink">
        <button class="nav__btn_sub" title="Яхты" data-type="nav_sub">
            Яхты 
            <?php get_template_part('svg/dropdown') ?>
        </button>
    </li>
    <li class="nav__li_sub">
        <ul class="nav__ul_sub">
            <?php if($GLOBALS['isSochi']) { ?>
            <li><a href="<?= get_home_url() ?>/catalog-yacht/" title="Все яхты в Сочи"><span>Яхты в Сочи</span></a></li>
            <li><a href="<?= get_home_url() ?>/catalog-yacht/arenda-motornoy-yachti/" title="Моторные яхты в Сочи"><span>Моторные яхты</span></a></li>
            <li><a href="<?= get_home_url() ?>/catalog-yacht/arenda-parusnoy-yachti/" title="Парусные яхты в Сочи"><span>Парусные яхты</span></a></li>
            <li><a href="<?= get_home_url() ?>/catalog-yacht/arenda-katamarana/" title="Катамараны в Сочи"><span>Катамараны</span></a></li>
            <li><a href="<?= get_home_url() ?>/catalog-yacht/arenda-teplohoda/" title="Теплоходы в Сочи"><span>Теплоходы</span></a></li>
            <li><a href="<?= get_home_url() ?>/catalog-yacht/arenda-katera/" title="Катера в Сочи"><span>Катера</span></a></li>
            <?php } ?>
            <?php if($GLOBALS['isAdler']) { ?>
            <li><a href="<?= get_home_url() ?>/adler/catalog-yacht-adler/" title="Все яхты в Адлере"><span>Яхты в Адлере</span></a></li>
            <li><a href="<?= get_home_url() ?>/adler/catalog-yacht-adler/arenda-motornoy-yachti/" title="Моторные яхты в Адлере">Моторные яхты</a></li>
            <li><a href="<?= get_home_url() ?>/adler/catalog-yacht-adler/arenda-parusnoy-yachti/" title="Парусные яхты в Адлере">Парусные яхты</a></li>
            <li><a href="<?= get_home_url() ?>/adler/catalog-yacht-adler/arenda-katera/" title="Катера в Адлере">Катера</a></li>
            <?php } ?>
            <?php if($GLOBALS['isLazarevskoe']) { ?>
            <li><a href="<?= get_home_url() ?>/lazarevskoe/catalog-yacht-lazarevskoe/" title="Все яхты в Лазаревском"><span>Яхты в Лазаревском</span></a></li>
            <?php } ?>
        </ul>
    </li>
    <?php if($GLOBALS['isSochi']) { ?>
    <li class="nav__sector">
        <a href="<?= get_home_url() ?>/ceny/" class="a_tomato" title="Цены">
            <span class="nav__titlewrap">
                <span class="nav__title">Цены</span>
            </span>
        </a>
    </li>
    <?php } ?>
    <?php if($GLOBALS['isAdler']) { ?>
    <li class="nav__sector">
        <a href="<?= get_home_url() ?>/adler/ceny/" class="a_tomato" title="Цены в Адлере">
            <span class="nav__titlewrap">
                <span class="nav__title">Цены</span>
            </span>
        </a>
    </li>
    <?php } ?>
    <li class="nav__sector">
        <a href="<?= get_home_url() ?>/uslugi/" class="a_skyblue" title="Услуги">
            <span class="nav__titlewrap">
                <span class="nav__title">Услуги</span>
            </span>
        </a>
    </li>
    <li class="nav__sector">
        <a href="<?= get_home_url() ?>/otzivi/" class="a_goldenrod" title="Отзывы">
            <span class="nav__titlewrap">
                <span class="nav__title">Отзывы</span>
            </span>
        </a>
    </li>
    <li class="nav__sector">
        <a href="<?= get_home_url() ?>/oplata/" class="a_deeppink" title="Оплата">
            <span class="nav__titlewrap">
                <span class="nav__title">Оплата</span>
            </span>
        </a>
    </li>
    <?php 
        get_template_part('parts/nav-more', '', ['layout' => 'desktop']); 
        get_template_part('parts/nav-more', '', ['layout' => 'mobile']);
    ?>     
</ul>