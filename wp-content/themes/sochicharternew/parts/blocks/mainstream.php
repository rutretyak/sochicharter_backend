<div class="mainstream" data-type="lazyload-bg" data-class="lazyload">
    <div class="container">
        <div class="row">

            <div class="mainstream__left col-12">
                <h1><?= get_query_var('mainstream_h1') ?></h1>
            </div>

            <div class="mainstream__right col-12">
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
                    get_template_part('parts/forms/frontform');
                ?>
            </div>
        
        </div>
    </div>
</div>