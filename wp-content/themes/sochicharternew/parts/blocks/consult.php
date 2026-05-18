<div class="consult">
    <div class="container">
        <div class="row">
            
            <div class="col-xl-7 col-lg-6 col-md-12 col-sm-12 col-12 consult__video">
                <?php get_template_part('parts/blocks/video') ?>
            </div>

            <div class="consult__block col-xl-5 col-lg-6 col-md-12 col-sm-12 col-12">
            <p class="col-12">
                <span>Узнайте как арендовать <br>яхту в <?= get_query_var('consult_city') ?><?php get_template_part('svg/arrow') ?></span>
            </p>
                <?php 
                    set_query_var('formname', 'consult-form');
                    set_query_var('formclass', 'consult__form');
                    set_query_var('form_title', 'Закажите обратный звонок');
                    set_query_var('form_subtitle', 'перезвоним в течение 5 мин.');
                    set_query_var('form_cta', 'Перезвоните мне');
                    set_query_var('forminfo', 'Форма консультации');
                    set_query_var('form_inputname', true);
                    set_query_var('form_inputphone', true);
                    set_query_var('form_inputname_id', 'consult__inputname');
                    set_query_var('form_inputphone_id', 'consult__inputphone');
                    set_query_var('form_asterisk', '* бесплатная консультация');
                    get_template_part('parts/forms/mainform');
                ?>
            </div>
        </div>
    </div>
</div>