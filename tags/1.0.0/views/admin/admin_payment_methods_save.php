<div class="row navbar">
    <div class="col-md-1">
        <a href="<?php echo esc_html($url);?>">Главная</a>
    </div>
    <div class="col-md-2">
        <a href="<?php echo esc_html($url.'&view=invoices');?>">Счета и платежи</a>
    </div>
    <div class="col-md-3">
        <a href="#" class="current">Настройка методов оплаты</a>
    </div>
    <div class="col-md-6"></div>
</div>
<div class="back_link">
    <a href="#" onclick="window.history.back()">Вернуться назад</a>
</div>
<input type="hidden" id="ajax-url" value="<?php echo esc_html($ajax_url); ?>" />
<form class="payment_setting_save_page" id="payment_setting_save_page" method="post" action="<?php echo esc_html($url); ?>&view=<?php echo esc_html(sanitize_text_field($_GET['view'])); ?>&id=<?php echo esc_html(sanitize_text_field($_GET['id']));  ?>">
    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_name">
                Название метода оплаты
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_name" name="payment_setting_name" required placeholder="Введите название метод оплаты" value="<?php echo esc_html(isset($param['Name']) ? $param['Name'] : ''); ?>" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_type">
                Тип метода оплаты
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <select id="payment_setting_type" name="payment_setting_type">
                <option>Выберите тип метода оплаты</option>
                <option <?php echo esc_html(isset($param['Type']) && $param['Type'] == 'ЕРИП' ? 'selected' : ''); ?>>ЕРИП</option>
                <option <?php echo esc_html(isset($param['Type']) && $param['Type'] == 'Интернет-эквайринг' ? 'selected' : ''); ?>>Интернет-эквайринг</option>
                <option <?php echo esc_html(isset($param['Type']) && $param['Type'] == 'E-POS' ? 'selected' : ''); ?>>E-POS</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_test_mode">
                Тестовый режим
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="checkbox" id="payment_setting_test_mode" name="payment_setting_test_mode" <?php echo esc_html(isset($param['TestMode']) && $param['TestMode'] == 1 ? 'checked' : '' )?> />
        </div>
    </div>

    <hr />

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_token">
                API ключ
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_token" name="payment_setting_token" required placeholder="Введите API ключ" value="<?php echo esc_html(isset($param['Token']) ? $param['Token'] : ''); ?>" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_service_id">
                Номер услуги
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_service_id" name="payment_setting_service_id" required placeholder="Введите номер услуги" value="<?php echo esc_html(isset($param['ServiceId']) ? $param['ServiceId'] : ''); ?>" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_notification_url">
                Адрес для уведомлений
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_notification_url" value="<?php echo esc_html($notif_url);?>" readonly />
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_secret_word">
                Секретное слово
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_secret_word" name="payment_setting_secret_word" placeholder="Введите секретное слово" value="<?php echo esc_html(isset($param['SecretWord']) ? $param['SecretWord'] : ''); ?>" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_use_signature_for_notification">
                Включить цифровую подпись для уведомлений
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="checkbox" id="payment_setting_use_signature_for_notification" name="payment_setting_use_signature_for_notification" <?php echo esc_html(isset($param['UseSignatureForNotification']) && $param['UseSignatureForNotification'] == 1 ? 'checked' : '') ?> />
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_secret_word_for_notification">
                Секретное слово для уведомлений
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_secret_word_for_notification" name="payment_setting_secret_word_for_notification" placeholder="Введите секретное слово для уведомлений" value="<?php echo esc_html(isset($param['SecretWordForNotification']) ? $param['SecretWordForNotification'] : ''); ?>" />
        </div>
    </div>

    <hr />

    <div class="other_setting" id="erip_setting">
        <div class="row"  id="showQrCodeContainer">
            <div class="col-md-3 col-xs-12">
                <label for="payment_setting_show_qr_code">
                    Показывать Qr-код
                </label>
            </div>
            <div class="col-md-9 col-xs-12">
                <input type="checkbox" id="payment_setting_show_qr_code" name="payment_setting_show_qr_code" <?php echo esc_html(isset($param['ShowQrCode']) && $param['ShowQrCode'] == 1 ? 'checked' : '') ?> />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <label for="payment_setting_can_change_name">
                    Разрешено измениять ФИО
                </label>
            </div>
            <div class="col-md-9 col-xs-12">
                <input type="checkbox" id="payment_setting_can_change_name" name="payment_setting_can_change_name" <?php echo esc_html(isset($param['CanChangeName']) && $param['CanChangeName'] == 1 ? 'checked' : '') ?> />

            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <label for="payment_setting_can_change_address">
                    Разрешено измениять адрес
                </label>
            </div>
            <div class="col-md-9 col-xs-12">
                <input type="checkbox" id="payment_setting_can_change_address" name="payment_setting_can_change_address" <?php echo esc_html(isset($param['CanChangeAddress']) && $param['CanChangeAddress'] == 1 ? 'checked' : '') ?> />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <label for="payment_setting_can_change_amount">
                    Разрешено измениять сумму
                </label>
            </div>
            <div class="col-md-9 col-xs-12">
                <input type="checkbox" id="payment_setting_can_change_amount" name="payment_setting_can_change_amount" <?php echo esc_html(isset($param['CanChangeAmount']) && $param['CanChangeAmount'] == 1 ? 'checked' : '') ?> />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <label for="payment_setting_send_email_notification">
                    Отправлять email-уведомление клиенту
                </label>
            </div>
            <div class="col-md-9 col-xs-12">
                <input type="checkbox" id="payment_setting_send_email_notification" name="payment_setting_send_email_notification" <?php echo esc_html(isset($param['SendEmail']) && $param['SendEmail'] == 1 ? 'checked' : '') ?> />
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 col-xs-12">
                <label for="payment_setting_send_sms_notification">
                    Отправлять sms-уведомление клиенту
                </label>
            </div>
            <div class="col-md-9 col-xs-12">
                <input type="checkbox" id="payment_setting_send_sms_notification" name="payment_setting_send_sms_notification" <?php echo esc_html(isset($param['SendSms']) && $param['SendSms'] == 1 ? 'checked' : '') ?> />
            </div>
        </div>

        <hr />
    </div>

    <div class="other_setting" id="epos_setting">
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <label for="payment_setting_service_provider_code">
                    Код производителя услуг
                </label>
            </div>
            <div class="col-md-9 col-xs-12">
                <input type="text" id="payment_setting_service_provider_code" name="payment_setting_service_provider_code" placeholder="Введите код производителя услуг" value="<?php echo esc_html(isset($param['ServiceProviderCode']) ? $param['ServiceProviderCode'] : ''); ?>" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <label for="payment_setting_service_epos_code">
                    Код услуги E-POS
                </label>
            </div>
            <div class="col-md-9 col-xs-12">
                <input type="text" id="payment_setting_service_epos_code" name="payment_setting_service_epos_code" placeholder="Введите код услуги E-POS" value="<?php echo esc_html(isset($param['ServiceEposCode']) ? $param['ServiceEposCode'] : ''); ?>" />
            </div>
        </div>
        <hr />
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_api_url">
                Адрес API
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_api_url" name="payment_setting_api_url" required placeholder="Введите адрес API" value="<?php echo esc_html(isset($param['ApiUrl']) ? $param['ApiUrl'] : ''); ?>" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_sandbox_url">
                Адрес тестового API
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_sandbox_url" name="payment_setting_sandbox_url" required placeholder="Введите адрес тестового API" value="<?php echo esc_html(isset($param['SandboxUrl']) ? $param['SandboxUrl'] : ''); ?>" />
        </div>
    </div>

    <div class="row" id="successMessageContainer">
        <div class="col-md-3 col-xs-12" >
            <label for="payment_setting_success_message" >
                Путь по ветке ЕРИП
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_success_message" name="payment_setting_success_message" placeholder="Введите сообщение после успешной операции" value="<?php echo esc_html(isset($param['SuccessMessage']) ? $param['SuccessMessage'] : ''); ?>" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-offset-5 col-md-7">
            <input class="button-blue button-action" type="submit" value="Сохранить">
            <input class="button-orange button-action" style="margin-left: 4px;" type="button" onclick="window.location.href='<?php echo esc_html($url.'&view=payment_setting'); ?>'" value="Отменить">
        </div>
    </div>

</form>