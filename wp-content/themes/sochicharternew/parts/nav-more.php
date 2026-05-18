    <?php
    $nav_more = $args['layout'];
    $nav_more_cls = "d-none d-xl-flex d-lg-flex";

    if($nav_more == "mobile") {
        $nav_more_cls = "d-flex d-xl-none d-lg-none";
    }
    ?>
    <li class="nav__sector nav__nolink <?= $nav_more_cls ?>">
        <button class="nav__btn_sub nav__btn_sub_dots" title="Ещё" data-type="nav_sub">
            <?= get_template_part('svg/menu/dots') ?>
        </button>
    </li>
    <li class="nav__li_sub">
        <ul class="nav__ul_sub">
            <li><a href="<?= get_home_url() ?>/akcii/" title="Дисконты, скидки и спецпредложения по аренде яхт"><span>Акции</span></a></li>
            <li><a href="<?= get_home_url() ?>/calculator/" title="Расчитать стоимость аренды"><span>Калькулятор</span></a></li>
            <li><a href="<?= get_home_url() ?>/marshruty/" title="Из Сочи на Яхте - Маршруты"><span>Маршруты</span></a></li>
            <li><a href="<?= get_home_url() ?>/blog/" title="Блог"><span>Блог</span></a></li>
            <li><a href="<?= get_home_url() ?>/about/" title="О Компании"><span>О Компании</span></a></li>
            <li><a href="<?= get_home_url() ?>/contacts/" title="Контакты"><span>Контакты</span></a></li>
        </ul>
    </li>