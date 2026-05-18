<footer class="footer">
    <div class="footer__totop"><?php get_template_part("/svg/dropdown") ?></div>
    <div class="footer__border"></div>
        <div class="container">

            <div class="footer__upper">
                <div class="footer__logo">
                    <?php get_template_part('svg/logo') ?>
                </div>
                <div class="footer__phone">
                    <a class="footer__link" href="tel:<?= $GLOBALS['phone_href_alt'] ?>" title="Связаться с нами по телефону <?= $GLOBALS['phone_rich_alt'] ?>">
                        <?php get_template_part('svg/phone') ?>
                        <span><?= $GLOBALS['phone_alt'] ?></span>
                    </a>
                </div>
                <div class="footer__email">
                    <a class="footer__link" href="mailto:marketing@sochicharter.ru" title="Написать нам на электронную почту">
                        <?php get_template_part('svg/email') ?>
                        <span>marketing@sochicharter.ru</span>
                    </a>
                </div>
                <div class="footer__social">
                    <?php get_template_part('parts/nav-social') ?>
                </div>
            </div>

            <div class="footer__addresses">
                <div class="footer__address footer__address_sochi">
                    <span>Адрес предоставления услуг в Сочи</span>
                    <div class="footer__address_block">
                        <?php get_template_part('svg/location') ?>
                        <address><?= $GLOBALS['address'] ?></address>
                    </div>
                </div>
                <div class="footer__address footer__address_adler">
                    <span>Адрес предоставления услуг в Адлере</span>
                    <div class="footer__address_block">
                        <?php get_template_part('svg/location') ?>
                        <address><?= $GLOBALS['address_adler'] ?></address>
                    </div>
                </div>
				<?php /*
                <div class="footer__address footer__address_ur">
                    <span>Юридический адрес</span>
                    <div class="footer__address_block">
                        <?php get_template_part('svg/location') ?>
                        <address><?= $GLOBALS['address_ur'] ?></address>
                    </div>
                </div>
				*/ ?>
            </div>

            <div class="footer__info">
                <?php /*
				<div class="footer__info_left">
                    <div class="footer__info_term">
                        <span class="footer__info_title">Индивидуальный предприниматель</span>
                        <span class="footer__info_data">ИВАННИКОВА ЕКАТЕРИНА АНДРЕЕВНА</span>
                    </div>
                    <div class="footer__info_term">
                        <span class="footer__info_title">ИНН</span>
                        <span class="footer__info_data">422108010065</span>
                    </div>
                    <div class="footer__info_term">
                        <span class="footer__info_title">ОГРН</span>
                        <span class="footer__info_data">323237500026427</span>
                    </div>
                </div>
				*/ ?>
                <div class="footer__info_right">
                    <p>Sochi Charter — ведущее российское чартерное агентство в Сочи, предоставляет услуги по аренде водного транспорта более 10 лет. Более 10.000 проведённых морских прогулок. Более 300 проведённых свадеб и торжественных мероприятий. Среди наших клиентов крупные компании, бизнесмены, а также актеры театра и кино.</p>
                    <p>Мы предлагаем электронные онлайн решения по бронированию яхт в Сочи. Сопровождаем полный спектр услуг от подбора яхты и организации праздников и вплоть до кастомных сценариев. Можем организовать дни рождения, корпоративы, свадьбы, кейтеринг, водные развлечения и профессиональные фотосессии.</p>
                </div>
            </div>

            <div class="footer__nav">
                <div class="footer__nav_col">
                    <div class="footer__nav_section">
                        <span class="footer__nav_title">Аренда яхт</span>
                        <ul>
                            <li><a href="<?= get_home_url(); ?>/" title="Аренда яхт в Сочи">Сочи</a></li>
                            <li><a href="<?= get_home_url(); ?>/adler/" title="Аренда яхт в Адлере">Адлер</a></li>
                            <li><a href="<?= get_home_url(); ?>/lazarevskoe/" title="Аренда яхт в Лазаревское">Лазаревское</a></li>
                        </ul>
                    </div>
                </div>
                <div class="footer__nav_col">
                    <div class="footer__nav_section">
                        <span class="footer__nav_title">Цены</span>
                        <ul>
                            <li><a href="<?= get_home_url(); ?>/akcii/" title="Акции, скидки и специальные предложения">Акции</a></li>
                            <li><a href="<?= get_home_url(); ?>/ceny/" title="Цены в Сочи">в Сочи</a></li>
                            <li><a href="<?= get_home_url(); ?>/adler/ceny/" title="Цены в Адлере">в Адлере</a></li>
                            <li><a href="<?= get_home_url(); ?>/calculator/" title="Калькулятор">Калькулятор</a></li>
                            <li><a href="<?= get_home_url(); ?>/oplata/" title="Оплата">Оплата</a></li>
                        </ul>
                    </div>
                </div>
                <div class="footer__nav_col">
                    <div class="footer__nav_section">
                        <span class="footer__nav_title">Компания</span>
                        <ul>
                            <li><a href="<?= get_home_url(); ?>/about/" title="О Нас">О Компании</a></li>
                            <li><a href="<?= get_home_url(); ?>/blog/" title="Полезные статьи на яхтенную тематику">Блог</a></li>
                            <li><a href="<?= get_home_url(); ?>/contacts/" title="Контактная информация">Контакты</a></li>
                        </ul>
                    </div>
                </div>
                <div class="footer__nav_col">
                    <div class="footer__nav_section">
                        <span class="footer__nav_title">Услуги</span>
                        <ul>
                            <li><a href="<?= get_home_url(); ?>/uslugi/uzhin-na-yachte/" title="Поужинать на яхте на фоне заката в море">Ужин на яхте</a></li>
                            <li><a href="<?= get_home_url(); ?>/uslugi/yacht-certificate/" title="Подарочный сертификат на аренду яхты">Подарочный сертификат</a></li>
                            <li><a href="<?= get_home_url(); ?>/uslugi/svadba-na-yahte-v-sochi/" title="Свадьба на яхте">Свадьба</a></li>
                            <li><a href="<?= get_home_url(); ?>/uslugi/den-rozdeniya/" title="Отметить день рождения на яхте">День рождения</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="footer__lower">
                <div class="footer__copy">
                &copy; 2014 - <?= date('Y'); ?> Официальный сайт чартерного агентства &laquo;SochiCharter&raquo; — аренда яхт в Сочи без посредников.
                </div>
                <div class="footer__privacy">
                    <a href="<?= get_home_url() ?>/privacy-policy/" title="Политика конфиденциальности">Политика конфиденциальности</a>
                </div>
            </div>

        </div>
    </div>
</footer>