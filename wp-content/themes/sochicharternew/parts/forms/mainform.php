			<form data-type="form" data-id="mainform" data-form="form" data-action="false" name="<?= get_query_var('formname') ?>" class="col form <?= get_query_var('formclass') ?>" action="<?= get_home_url() ?>/success" autocomplete="on" method="post" title="Отправить заявку" novalidate>
            	<fieldset>
                <legend><?= get_query_var('form_title') ?></legend>
                <span class="form__sublegend"><?= get_query_var('form_subtitle') ?></span>

                <?php if(get_query_var('form_inputname') == true) { ?>
                <div class="form-auth__group form-auth__group_name" data-type="form-group">
                    <span class="form-auth__errtext" data-type="form-errtext"></span>
                    <div class="form-auth__valid" data-type="form-valid"></div>
                    <div class="form-auth__error" data-type="form-error"><?php get_template_part('svg/error'); ?></div>
                    <input 
                        id="<?= get_query_var('form_inputname_id') ?>"
                        name="inputname" 
                        type="text" 
                        class="col input input_field input_m input-name" 
                        placeholder="Ваше Имя" 
                        data-placeholder="Ваше Имя"
                        data-type="ui-input"
                        data-required="true"
                        data-validation="name">
                </div>
                <?php } ?>

                <?php if(get_query_var('form_inputphone') == true) { ?>
                <div class="form-auth__group form-auth__group_phone" data-type="form-group">
                    <span class="form-auth__errtext" data-type="form-errtext"></span>
                    <div class="form-auth__valid" data-type="form-valid"></div>
                    <div class="form-auth__error" data-type="form-error"><?php get_template_part('svg/error'); ?></div>
                    <input 
                        id="<?= get_query_var('form_inputphone_id') ?>"
                        name="inputphone" 
                        type="tel" 
                        class="col input input_field input_m input-phone" 
                        placeholder="Ваш телефон" 
                        data-placeholder="Ваш телефон"
                        data-type="ui-input"
                        data-required="true"
                        data-validation="phone"
                        data-phonemask="phonemask">
                </div>
                <?php } ?>
                
                <input type="hidden" name="formuri" value="<?= $_SERVER['REQUEST_URI'] ?>">
                <input type="hidden" name="forminfo" value="<?= get_query_var('forminfo') ?>">
                <input type="hidden" name="token" value="<?= rand(10000, 99999) ?>">

                <button type="submit" class="col button button_submit transitioned" title="Отправить заявку">
                    <span class="button__icon"><?php get_template_part('svg/hand'); ?></span>
                    <span class="col button__text"><?= get_query_var('form_cta') ?></span>
                </button>
                <span class="form__asterisk"><?= get_query_var('form_asterisk') ?></span>
                </fieldset>
            </form>