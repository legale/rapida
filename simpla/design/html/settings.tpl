{capture name=tabs}
    <li class="active"><a href="?module=SettingsAdmin">Настройки</a></li>
    {if isset($userperm['currency'])}
        <li><a href="?module=CurrencyAdmin">Валюты</a></li>
    {/if}
    {if isset($userperm['delivery'])}
        <li><a href="?module=DeliveriesAdmin">Доставка</a></li>
    {/if}
    {if isset($userperm['payment'])}
        <li><a href="?module=PaymentMethodsAdmin">Оплата</a></li>
    {/if}
{/capture}

{$meta_title = "Настройки" scope=root}

{if $message_success}
    <!-- Системное сообщение -->
    <div class="message message_success">
        <span class="text">{if $message_success == 'saved'}Настройки сохранены{/if}</span>
        {if $smarty.get.return}
            <a class="button" href="{$smarty.get.return}">Вернуться</a>
        {/if}
    </div>
    <!-- Системное сообщение (The End)-->
{/if}

{if $message_error}
    <!-- Системное сообщение -->
    <div class="message message_error">
        <span class="text">{if $message_error == 'overlay_is_not_writable'}Установите права на запись для файла {$config->images['overlay_file']}{/if}</span>
        <a class="button" href="">Вернуться</a>
    </div>
    <!-- Системное сообщение (The End)-->
{/if}


<!-- Основная форма -->
<form method=post class=product enctype="multipart/form-data">
    <input type=hidden name="session_id" value="{$smarty.session.id}">
    <div class="block">
        <h2>Настройки системы</h2>
        <ul>

            <li>
                <label class="property">Пропускать создание заданий в таблице queue_full</label>
                <input class="switcher__input" value="true" type="checkbox" name="skip_queue_full"
                       id="skip_queue_full_switch"
                       {if $config->cache['skip_queue_full']}checked{/if}>
            </li>
            <li>
                <label class="property">Кеширование запросов к БД</label>
                <input class="switcher__input" value="true" type="checkbox" name="cache" id="cache_switch"
                       {if $config->cache['enabled']}checked{/if}>
            </li>

            <li>
                <label class="property">Способ сохранения кеша на диск</label>
                <select name="method">
                    <option value="json" {if $config->cache['method'] === 'json'}selected{/if}>json</option>
                    <option value="serialize" {if $config->cache['method'] === 'serialize'}selected{/if}>serialize
                    </option>
                    <option value="var_export" {if $config->cache['method'] === 'var_export'}selected{/if}>var_export
                    </option>
                    <option value="msgpack" {if $config->cache['method'] === 'msgpack'}selected{/if}>msgpack</option>
                </select>
            </li>

            <li>
                <label class="property">Отладчик работы системы (Появляется в конце каждой страницы)</label>
                <input class="switcher__input" value="true" type="checkbox" name="debug" id="debug_switch"
                       {if $config->debug}checked{/if}>
            </li>
            <li>
                <label class="property">Капча общая</label>
                <input class="switcher__input" value="true" type="checkbox" name="captcha" 
                       {if $config->captcha}checked{/if}>
            </li>
            <li>
                <label class="property">Капча в заказах</label>
                <input class="switcher__input" value="true" type="checkbox" name="captcha_order" 
                       {if $config->captcha_order}checked{/if}>
            </li>
            <li>
                <label class="property">Дата, начиная с которой кеш не обновляется</label>
                <textarea disabled class="simpla_inp">{date("d-m-y H:m:s",$config->cache_date)}</textarea>
                <button type="submit" name="update_cache_date" class="simpla_inp" value="1">Обновить дату</button>
            </li>
        </ul>
    </div>
    <!-- Счетчики -->
    <div class="block layer">
        <h2>Коды счетчиков</h2>
        <ul>
            <li><label class=property>Яндекс метрика</label>
                <textarea name="yandex_metric" class="simpla_inp">{$settings->yandex_metric|escape}</textarea>
            </li>
        </ul>
    </div>
    <!-- Параметры -->
    <div class="block layer">
        <h2>Настройки сайта</h2>
        <ul>
            <li><label class=property>Имя сайта</label><input name="site_name" class="simpla_inp" type="text"
                                                              value="{$settings->site_name|escape}"/></li>
            <li><label class=property>Имя компании</label><input name="company_name" class="simpla_inp" type="text"
                                                                 value="{$settings->company_name|escape}"/></li>
            <li><label class=property>Телефон</label><input name="phone" class="simpla_inp" type="text"
                                                            value="{$settings->phone|escape}"/></li>
            <li><label class=property>Адрес</label><input name="address" class="simpla_inp" type="text"
                                                          value="{$settings->address|escape}"/></li>
            <li><label class=property>Формат даты</label><input name="date_format" class="simpla_inp" type="text"
                                                                value="{$settings->date_format|escape}"/></li>
            </li>
        </ul>
    </div>
    <div class="block layer">
        <h2>Почтовые адреса</h2>
        <ul>
            <li><label class=property>Email для восстановления пароля</label><input name="admin_email"
                                                                                    class="simpla_inp" type="text"
                                                                                    value="{$settings->admin_email|escape}"/>
            <li><label class=property>Оповещение о заказах</label><input name="order_email" class="simpla_inp"
                                                                         type="text"
                                                                         value="{$settings->order_email|escape}"/></li>
            <li><label class=property>Оповещение о комментариях</label><input name="comment_email" class="simpla_inp"
                                                                              type="text"
                                                                              value="{$settings->comment_email|escape}"/>
            </li>
            <li><label class=property>Обратный адрес оповещений</label><input name="notify_from_email"
                                                                              class="simpla_inp" type="text"
                                                                              value="{$settings->notify_from_email|escape}"/>
            </li>
        </ul>
    </div>
    <!-- Параметры (The End)-->

    <!-- Параметры -->
    <div class="block layer">
        <h2>Формат цены</h2>
        <ul>
            <li><label class=property>Разделитель копеек</label>
                <select name="decimals_point" class="simpla_inp">
                    <option value='.' {if $settings->decimals_point == '.'}selected{/if}>точка:
                        12.45 {$currency['sign']|escape}</option>
                    <option value=',' {if $settings->decimals_point == ','}selected{/if}>запятая:
                        12,45 {$currency['sign']|escape}</option>
                </select>
            </li>
            <li><label class=property>Разделитель тысяч</label>
                <select name="thousands_separator" class="simpla_inp">
                    <option value='' {if $settings->thousands_separator == ''}selected{/if}>без разделителя:
                        1245678 {$currency['sign']|escape}</option>
                    <option value=' ' {if $settings->thousands_separator == ' '}selected{/if}>пробел: 1 245
                        678 {$currency['sign']|escape}</option>
                    <option value=',' {if $settings->thousands_separator == ','}selected{/if}>запятая:
                        1,245,678 {$currency['sign']|escape}</option>
                </select>


            </li>
        </ul>
    </div>
    <!-- Параметры (The End)-->

    <!-- Параметры -->
    <div class="block layer">
        <h2>Настройки каталога</h2>
        <ul>
            <li><label class=property>Товаров на странице сайта</label><input name="products_num" class="simpla_inp"
                                                                              type="text"
                                                                              value="{$settings->products_num|escape}"/>
            </li>
            <li><label class=property>Товаров на странице админки</label><input name="products_num_admin"
                                                                                class="simpla_inp" type="text"
                                                                                value="{$settings->products_num_admin|escape}"/>
            </li>
            <li><label class=property>Максимум товаров в заказе</label><input name="max_order_amount" class="simpla_inp"
                                                                              type="text"
                                                                              value="{$settings->max_order_amount|escape}"/>
            </li>
            <li><label class=property>Единицы измерения товаров</label><input name="units" class="simpla_inp"
                                                                              type="text"
                                                                              value="{$settings->units|escape}"/></li>
        </ul>
    </div>
    <!-- Параметры (The End)-->

    <!-- Параметры -->
    <div class="block layer">
        <h2>Изображения товаров</h2>

        <ul>
            <li><label class=property>Водяной знак</label>
                <input name="overlay_file" class="simpla_inp" type="file"/>

                <img style='display:block; border:1px solid #d0d0d0; margin:10px 0 10px 0;'
                     src="{$config->root_url}/{$config->images['overlay_file']}?{math equation='rand(10,10000)'}">
            </li>
            <li><label class=property>Размер водяного знака от итогового изображения в %</label><input
                        name="overlay_ratio"
                        class="simpla_inp"
                        type="text"
                        value="{$settings->overlay_ratio}"/>

            </li>
            <li><label class=property>Горизонтальное положение водяного знака в %</label>
                <input name="overlay_offset_x"
                       class="simpla_inp"
                       type="text"
                       value="{$settings->overlay_offset_x}"/>

            </li>
            <li><label class=property>Вертикальное положение водяного знака в %</label>
                <input name="overlay_offset_y"
                       class="simpla_inp"
                       type="text"
                       value="{$settings->overlay_offset_y}"/>

            </li>
            <li><label class=property>Видимость(прозрачность) знака (меньше &mdash; прозрачнее) %</label><input
                        name="overlay_opacity" class="simpla_inp" type="text"
                        value="{$settings->overlay_opacity|escape}"/>
            </li>
            <li>
                <label class=property>Резкость изображений в % (рекомендуется 20%)</label>
                <input name="images_sharpness" class="simpla_inp" type="text" value="{$settings->images_sharpness}"/>
            </li>
            <li>
                <label class=property>Коэффициент обрезания изображения 1 - не обрезать, 1.25 - обрезать на 25%</label>
                <input name="crop_factor" class="simpla_inp" type="text" value="{$config->images['crop_factor']}"/>
            </li>
            <li>
                <label class=property>Цвет заливки краев изображения в формате RGB через запятую</label>
                <input name="bg_color" class="simpla_inp" type="text" value="{$config->images['bg_color']}"/>
            </li>
            <li>
                <label class=property>Пытаться использовать Imagick вместо GD</label>
                <input name="imagick" class="simpla_inp" type="checkbox" {if $config->images['imagick']}checked{/if}
                       value="1"/>
            </li>
        </ul>
    </div>
    <!-- Параметры (The End)-->

    <input class="button_green button_save" type="submit" name="save" value="Сохранить"/>

    <!-- Левая колонка свойств товара (The End)-->

</form>
<!-- Основная форма (The End) -->
