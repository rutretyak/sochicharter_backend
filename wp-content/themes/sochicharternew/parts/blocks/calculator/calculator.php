
<div class="calculator col-12" data-type="calculator">

<div class="row no-gutters">
    <div class="col calculator__tabs" data-type="calculator_tabs">

        <div class="calculator__tab" data-type="calculator_tab" data-tabid="1">
            <div class="calc__steps">
                Шаг: 1 из 3
            </div>
            
            <form>
                <div class="row">
                    <div class="calc__selectrow col-xl-2 col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="sf__field sf__field_dropdown">
                            <?php get_template_part('svg/dropdown') ?>
                            <select id="calc_s1_city" name="calc_s1_city">
                                <option value="1">Сочи</option>
                                <option value="2">Адлер</option>
                                <option value="3">Лазаревское</option>
                            </select>
                        </div>
                    </div>

                    <div class="calc__selectrow col-xl-2 col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="sf__field sf__field_dropdown">
                            <?php get_template_part('svg/dropdown') ?>
                            <select id="calc_s1_type" name="calc_s1_type">
                                <option value="0">Все яхты</option>
                                <option value="1">Моторные</option>
                                <option value="2">Парусные</option>
                                <option value="3">Катамараны</option>
                                <option value="4">Теплоходы</option>
                                <option value="5">Катера</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row calc__row calc__row_11">
                    <span class="calc__label col-xl-2 col-lg-2">Цена</span>
                    <div class="col-xl-6 col-lg-7">
                        <input type="text" id="calc_s1_price" name="calc_s1_price" value="" />
                    </div>
                </div>
                
                <div class="row calc__row">
                    <span class="calc__label col-xl-2 col-lg-2">Пассажиров</span>
                    <div class="col-xl-6 col-lg-7">
                        <input type="text" id="calc_s1_capacity" name="calc_s1_capacity" value="" />
                    </div>
                </div>
                
                <div class="row calc__row">
                    <span class="calc__label col-xl-2 col-lg-2">Кают</span>
                    <div class="col-xl-6 col-lg-7">
                        <input type="text" id="calc_s1_cabin" name="calc_s1_cabin" value="" />
                    </div>
                </div>

                <div class="row calc__row calc__row_14">
                    <div class="col-xl-2 offset-xl-6 col-lg-2 offset-lg-7 col-md-4 offset-md-8 col-sm-6 offset-sm-3 col-12 offset-0">
                        <button type="button" class="calc__btn" id="calc_s1_apply" title="Применить настройки фильтрации по яхтам"><span>&check; Ок</span></button>
                    </div>
                </div>

            </form>

            <div class="table-responsive">
            <table id="calc_s1_table" class="table">
                <thead>
                    <tr>
                        <td>Фото</td>
                        <td>Название</td>
                        <td>Цена</td>
                        <td>Выбрать</td>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">* Выберите яхту и нажмите кнопку "Далее &rarr;", чтобы расчитать стоимость Вашего мероприятия</td>
                    </tr>
                </tfoot>
            </table>
            </div>

        </div>

        <?php
        /*
            TAB 2: Selecting date, time & event type
        */
        ?>
        <div class="calculator__tab" data-type="calculator_tab" data-tabid="2">
            <div class="calc__steps">
                Шаг: 2 из 3
            </div>

            <div class="row calc__row calc__row_11">
                <span class="calc__label col-xl-2 col-lg-2">Дата и время</span>
                <div class="calc__selectrow col-xl-3 col-lg-4 col-md-6 col-sm-12 col-12 offset-xl-0 offset-lg-0 offset-md-3 offset-sm-0 offset-0">
                    <div class="sf__field sf__field_dropdown sf__field_flatpickr">
                        <?php get_template_part('svg/calculator/calendar') ?>
                        <div id="calc_s2_date" name="calc_s2_date" class="flatpickr col">
                            <input type="text" class="col calc__flatpickr" placeholder="Выберите дату..." data-input>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row calc__row calc__row_11">
                <span class="calc__label col-xl-2 col-lg-2">Часов в море</span>
                <div class="col-xl-6 col-lg-7">
                    <input type="text" id="calc_s2_hours" name="calc_s2_hours" value="" />
                </div>
            </div>

            <span class="calc__eventsh">Выберите своё мероприятие</span>

            <div class="calc__events row no-gutters">
                <div class="calc__eventrow">
                    <input type="radio" id="calc_s2_event1" name="calc_s2_event" value="1" checked data-title="Индивидуальная морская прогулка">
                    <label for="calc_s2_event1">
                        <?php get_template_part('svg/calculator/event-1') ?>
                        <span>Индивидуальная морская прогулка</span>
                    </label>
                </div>
                <div class="calc__eventrow">
                    <input type="radio" id="calc_s2_event2" name="calc_s2_event" value="2" data-title="Морская рыбалка">
                    <label for="calc_s2_event2">
                        <?php get_template_part('svg/calculator/event-2') ?>
                        <span>Морская рыбалка</span>
                    </label>
                </div>
                <div class="calc__eventrow">
                    <input type="radio" id="calc_s2_event3" name="calc_s2_event" value="3" data-title="Ужин на яхте">
                    <label for="calc_s2_event3">
                        <?php get_template_part('svg/calculator/event-3') ?>
                        <span>Ужин на яхте</span>
                    </label>
                </div>
                <div class="calc__eventrow">
                    
                    <input type="radio" id="calc_s2_event4" name="calc_s2_event" value="4" data-title="Завтрак в море">
                    <label for="calc_s2_event4">
                        <?php get_template_part('svg/calculator/event-4') ?>
                        <span>Завтрак в море</span>
                    </label>
                </div>
                <div class="calc__eventrow">
                    <input type="radio" id="calc_s2_event5" name="calc_s2_event" value="5" data-title="Морская прогулка с обедом">
                    <label for="calc_s2_event5">
                        <?php get_template_part('svg/calculator/event-5') ?>
                        <span>Морская прогулка с обедом</span>
                    </label>
                </div>
                <div class="calc__eventrow">
                    <input type="radio" id="calc_s2_event6" name="calc_s2_event" value="6" data-title="Праздник в море( День Рождения, юбилей, годовщина свадьбы)">
                    <label for="calc_s2_event6">
                        <?php get_template_part('svg/calculator/event-6') ?>
                        <span>Праздник в море( День Рождения, юбилей, годовщина свадьбы)</span>
                    </label>
                </div>
                <div class="calc__eventrow">
                    <input type="radio" id="calc_s2_event7" name="calc_s2_event" value="7" data-title="Свадьба в море">
                    <label for="calc_s2_event7">
                        <?php get_template_part('svg/calculator/event-7') ?>
                        <span>Свадьба в море</span>
                    </label>
                </div>
                <div class="calc__eventrow">
                    <input type="radio" id="calc_s2_event8" name="calc_s2_event" value="8" data-title="Предложение руки и сердца">
                    <label for="calc_s2_event8">
                        <?php get_template_part('svg/calculator/event-8') ?>
                        <span>Предложение руки и сердца</span>
                    </label>
                </div>
                <div class="calc__eventrow">
                    
                    <input type="radio" id="calc_s2_event9" name="calc_s2_event" value="9" data-title="Мальчишник или девичник">
                    <label for="calc_s2_event9">
                        <?php get_template_part('svg/calculator/event-9') ?>
                        <span>Мальчишник или девичник</span>
                    </label>
                </div>
                <div class="calc__eventrow">
                    <input type="radio" id="calc_s2_event10" name="calc_s2_event" value="10" data-title="Фотосессия на яхте в море">
                    <label for="calc_s2_event10">
                        <?php get_template_part('svg/calculator/event-10') ?>
                        <span>Фотосессия на яхте в море</span>
                    </label>
                </div>
            </div>

            <div class="row">
                <button type="button" class="calc__btn calc__btn_back col-xl-2 offset-xl-4 col-lg-2 offset-lg-4 col-md-4 offset-md-2 col-sm-4 offset-sm-2 col-6 offset-0" id="calc_s2_back" title="Вернуться к выбору яхт">&larr; Назад</button>
                <button type="button" class="calc__btn col-xl-2 offset-xl-0 col-lg-2 offset-lg-0 col-md-4 offset-md-0 col-sm-4 offset-sm-0 col-6 offset-0" id="calc_s2_submit" title="Перейти к выбору дополнительных услуг">&#10003; Ок</button>
            </div>

        </div>

        <?php
        /*
            TAB 3: Selecting additional services
        */
        ?>
        <div class="calculator__tab" data-type="calculator_tab" data-tabid="3">

            <div class="calc__steps">
                Шаг: 3 из 3
            </div>

            <div class="row calc__service-wrap">

                <div class="col-xl-4 col-lg-4">
                    <span class="calc__service-h">Еда</span>
                    <div id="calc_s3_food" data-type="multiselect-food" data-title="Еда"></div>
                </div>

                <div class="col-xl-4 col-lg-4">
                <div>
                    <span class="calc__service-h">Доставка цветов</span>
                    <div class="sf__field sf__field_dropdown">
                        <?php get_template_part('svg/dropdown') ?>
                        <select id="calc_s3_flowers" data-title="Цветы">
                            <option value="0">Выберите букеты</option>
                            <option value="3000" data-title="Мини букет">Мини букет (3000 руб)</option>
                            <option value="5000" data-title="Средний букет">Средний букет (5000 руб)</option>
                            <option value="7000" data-title="Большой букет">Большой букет (7000 руб)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <span class="calc__service-h calc__service-h1">Украшение яхты</span>
                    <div id="calc_s3_pretty" data-type="multiselect-pretty" data-title="Украшение"></div>
                    
                    <span class="calc__service-h calc__service-h1">Украшение свадебное</span>
                    <div class="sf__field sf__field_dropdown">
                        <?php get_template_part('svg/dropdown') ?>
                        <select id="calc_s3_prettyw" data-title="Свадебные цветы">
                            <option value="0">Выберите свадебное украшение</option>
                            <option value="3000" data-title="Сетка 'Сердце с шарами'">Сетка "Сердце с шарами" (3000 руб)</option>
                            <option value="5000" data-title="Сетка 'Сердце с шарами'">Сетка "Сердце с шарами" (5000 руб)</option>
                            <option value="7000" data-title="Сетка 'Сердце с шарами'">Сетка "Сердце с шарами" (7000 руб)</option>
                        </select>
                    </div>
                </div>
                </div>

                <div class="col-xl-4 col-lg-4">
                <div>
                    <span class="calc__service-h calc__service-hm">Развлечения</span>
                    <div id="calc_s3_service" data-type="multiselect-service" data-title="Дополнительные услуги"></div>
                </div>

                <div>
                    <label class="calc__checkbox" for="calc_s3_tamada">
                        <input id="calc_s3_tamada" type="checkbox" title="Ведущий мероприятий на яхту" data-title="Ведущий" data-price="10000">
                        <span>Ведущий (10000 руб)</span>
                    </label>
                </div>

                <div>
                    <label class="calc__checkbox" for="calc_s3_music">
                        <input id="calc_s3_music" type="checkbox" title="Музыка с собой на флешке или та, что есть на яхте" data-title="Музыка" data-price="0">
                        <span>Музыка (Бесплатно)</span>
                    </label>
                </div>
                </div>

            </div>

            <div class="row">
                <button type="button" class="calc__btn calc__btn_back col-xl-2 offset-xl-4 col-lg-3 offset-lg-3 col-md-4 offset-md-2 col-sm-4 offset-sm-2 col-6 offset-0" id="calc_s3_back" title="Вернуться к выбору услуг">&larr; Назад</button>
                <button type="button" class="calc__btn col-xl-2 offset-xl-0 col-lg-3 offset-lg-0 col-md-4 offset-md-0 col-sm-4 offset-sm-0 col-6 offset-0" id="calc_s3_submit" title="Рассчитать стоимость мероприятия">&#10003; Рассчитать</button>
            </div>

        </div>

        <?php
        /*
            TAB 3: Render results
        */
        ?>
        <div class="calculator__tab" data-type="calculator_tab" data-tabid="4">
            <div class="table-responsive">
                <table id="calc_s4_table" class="table calc__tbl col-xl-8 offset-xl-2 col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-sm-12 offset-sm-0 col-12 offset-0">
                    <caption>Расчётный лист мероприятия</caption>
                    <thead>
                        <tr>
                            <td>Услуга</td>
                            <td>Цена (руб)</td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>

</div>