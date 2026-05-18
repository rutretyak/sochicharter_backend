<main class="main" data-type="lazyload-bg" data-class="lazyload">
<div class="container">
    <div class="row">

            <div class="main__left col-xl-8 col-lg-7 col-md-6 col-sm-12">
                <?= get_query_var('main_h1') ?>
                <div class="main__h3 d-xl-block d-lg-block d-md-block d-none">Закажите заранее, получите <span>скидку 3%</span></div>
                <div class="main__h2 d-xl-block d-lg-block d-md-block d-none">&laquo;Подарите себе 
                незабываемое 
                морское путешествие!&raquo;</div>
            </div>

            <div class="main__right col-xl-4 offset-xl-0 col-lg-5 offset-lg-0 col-md-6 offset-md-0 col-sm-8 offset-sm-2 col-12 offset-xs-0">
                <?php 
                    set_query_var('formname', 'main-form');
                    set_query_var('formclass', 'main__form');
                    set_query_var('form_title', 'Оставьте заявку');
                    set_query_var('form_subtitle', 'Перезвоним &lt; 10 мин!');
                    set_query_var('form_cta', 'Забронировать!');
                    set_query_var('forminfo', 'Главная форма');
                    set_query_var('form_inputname', true);
                    set_query_var('form_inputphone', true);
                    set_query_var('form_inputname_id', 'main__inputname');
                    set_query_var('form_inputphone_id', 'main__inputphone');
                    set_query_var('form_asterisk', '* консультируем бесплатно');
                    get_template_part('parts/forms/mainform');
                ?>
            </div>
    </div>
</div>
</main>
