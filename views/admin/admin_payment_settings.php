<div class="row navbar">
    <div class="col-md-2">
        <a href="<?php echo esc_html($url . '?page=expresspay-payment'); ?>"><?php esc_html_e('home', 'express-pay') ?></a>
    </div>
    <div class="col-md-2">
        <a href="<?php echo esc_html($url . '?page=invoices-and-payments'); ?>"><?php esc_html_e('invoices-and-payments', 'express-pay') ?></a>
    </div>
    <div class="col-md-2">
        <a href="<?php echo esc_html($url . '?page=payment-settings-list'); ?>" class="current"><?php esc_html_e('settings', 'express-pay') ?></a>
    </div>
    <div class="col-md-2">
        <a target="_blank" href="<?php echo esc_html('https://express-pay.by/extensions/wordpress/erip'); ?>"><?php esc_html_e('help', 'express-pay') ?></a>
    </div>
    <div class="col-md-6"></div>
</div>
<div class="back_link">
    <a href="#" onclick="window.history.back()"><?php esc_html_e('back', 'express-pay') ?></a>
</div>
<input type="hidden" id="ajax-url" value="<?php echo esc_html($ajax_url); ?>" />
<form class="payment_setting_save_page" id="payment_setting_save_page" method="post" action="<?php echo esc_html($url); ?>">
    <input type="hidden" name="id" value="<?php echo intval($id); ?>" />
    <?php wp_nonce_field('expresspay_payment_settings_nonce', 'expresspay_nonce'); ?>
    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_name">
                <?php esc_html_e('payment-method-name', 'express-pay') ?>
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_name" name="payment_setting_name" required placeholder="<?php esc_html_e('enter-name-of-payment-method', 'express-pay') ?>" value="<?php echo esc_html(isset($param['Name']) ? $param['Name'] : ''); ?>" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_type">
                <?php esc_html_e('payment-method-type', 'express-pay') ?>
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <select id="payment_setting_type" name="payment_setting_type" required>
                <option disabled value="" selected hidden><?php esc_html_e('select-type-of-payment-method', 'express-pay') ?></option>
                <option value="erip" <?php echo esc_html(isset($param['Type']) && $param['Type'] == 'erip' ? 'selected' : ''); ?>><?php esc_html_e('erip', 'express-pay') ?></option>
                <option value="card" <?php echo esc_html(isset($param['Type']) && $param['Type'] == 'card' ? 'selected' : ''); ?>><?php esc_html_e('internet-acquiring', 'express-pay') ?></option>
                <option value="epos" <?php echo esc_html(isset($param['Type']) && $param['Type'] == 'epos' ? 'selected' : ''); ?>><?php esc_html_e('epos', 'express-pay') ?></option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_test_mode">
                <?php esc_html_e('test-mode', 'express-pay') ?>
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="checkbox" id="payment_setting_test_mode" name="payment_setting_test_mode" <?php echo esc_html(isset($param['TestMode']) && $param['TestMode'] == 1 ? 'checked' : '') ?> />
        </div>
    </div>

    <hr />

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_token">
                <?php esc_html_e('api-key', 'express-pay') ?>
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_token" name="payment_setting_token" required placeholder="<?php esc_html_e('enter-api-key', 'express-pay') ?>" value="<?php echo esc_html(isset($param['Token']) ? $param['Token'] : ''); ?>" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_service_id">
                <?php esc_html_e('service-number', 'express-pay') ?>
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_service_id" name="payment_setting_service_id" required placeholder="<?php esc_html_e('enter-service-number', 'express-pay') ?>" value="<?php echo esc_html(isset($param['ServiceId']) ? $param['ServiceId'] : ''); ?>" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_notification_url">
                <?php esc_html_e('address-for-notifications', 'express-pay') ?>
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_notification_url" value="<?php echo esc_html($notif_url); ?>" readonly />
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_secret_word">
                <?php esc_html_e('secret-word', 'express-pay') ?>
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_secret_word" name="payment_setting_secret_word" placeholder="<?php esc_html_e('enter-secret-word', 'express-pay') ?>" value="<?php echo esc_html(isset($param['SecretWord']) ? $param['SecretWord'] : ''); ?>" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_use_signature_for_notification">
                <?php esc_html_e('enable-digital-signature-for-notifications', 'express-pay') ?>
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="checkbox" id="payment_setting_use_signature_for_notification" name="payment_setting_use_signature_for_notification" <?php echo esc_html(isset($param['UseSignatureForNotification']) && $param['UseSignatureForNotification'] == 1 ? 'checked' : '') ?> />
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_secret_word_for_notification">
                <?php esc_html_e('secret-word-for-notifications', 'express-pay') ?>
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_secret_word_for_notification" name="payment_setting_secret_word_for_notification" placeholder="<?php esc_html_e('enter-secret-word-for-notifications', 'express-pay') ?>" value="<?php echo esc_html(isset($param['SecretWordForNotification']) ? $param['SecretWordForNotification'] : ''); ?>" />
        </div>
    </div>

    <hr />


    <div id="erip_setting">
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <label for="payment_setting_show_qr_code">
                    <?php esc_html_e('show-qr-code', 'express-pay') ?>
                </label>
            </div>
            <div class="col-md-9 col-xs-12">
                <input type="checkbox" id="payment_setting_show_qr_code" value="1" name="payment_setting_show_qr_code" <?php echo esc_html(isset($param['ShowQrCode']) && $param['ShowQrCode'] == 1 ? 'checked' : '') ?> />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <label for="payment_setting_can_change_name">
                    <?php esc_html_e('allowed-to-change-name', 'express-pay') ?>
                </label>
            </div>
            <div class="col-md-9 col-xs-12">
                <input type="checkbox" id="payment_setting_can_change_name" name="payment_setting_can_change_name" <?php echo esc_html(isset($param['CanChangeName']) && $param['CanChangeName'] == 1 ? 'checked' : '') ?> />

            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <label for="payment_setting_can_change_address">
                    <?php esc_html_e('allowed-to-change-address', 'express-pay') ?>
                </label>
            </div>
            <div class="col-md-9 col-xs-12">
                <input type="checkbox" id="payment_setting_can_change_address" name="payment_setting_can_change_address" <?php echo esc_html(isset($param['CanChangeAddress']) && $param['CanChangeAddress'] == 1 ? 'checked' : '') ?> />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <label for="payment_setting_can_change_amount">
                    <?php esc_html_e('allowed-to-change-amount', 'express-pay') ?>
                </label>
            </div>
            <div class="col-md-9 col-xs-12">
                <input type="checkbox" id="payment_setting_can_change_amount" name="payment_setting_can_change_amount" <?php echo esc_html(isset($param['CanChangeAmount']) && $param['CanChangeAmount'] == 1 ? 'checked' : '') ?> />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <label for="payment_setting_send_email_notification">
                    <?php esc_html_e('send-email-notification-to-client', 'express-pay') ?>
                </label>
            </div>
            <div class="col-md-9 col-xs-12">
                <input type="checkbox" id="payment_setting_send_email_notification" name="payment_setting_send_email_notification" <?php echo esc_html(isset($param['SendEmail']) && $param['SendEmail'] == 1 ? 'checked' : '') ?> />
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 col-xs-12">
                <label for="payment_setting_send_sms_notification">
                    <?php esc_html_e('send-sms-notification-to-the-client', 'express-pay') ?>
                </label>
            </div>
            <div class="col-md-9 col-xs-12">
                <input type="checkbox" id="payment_setting_send_sms_notification" name="payment_setting_send_sms_notification" <?php echo esc_html(isset($param['SendSms']) && $param['SendSms'] == 1 ? 'checked' : '') ?> />
            </div>
        </div>

        <hr />
    </div>

    <div class="row" id="erip_setting_path">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_erip_path">
                <?php esc_html_e('path-along-the-erip-branch', 'express-pay') ?>
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_erip_path" name="payment_setting_erip_path" placeholder="<?php esc_html_e('enter-path-along-the-erip-branch', 'express-pay') ?>" value="<?php echo esc_html(isset($param['EripPath']) ? $param['EripPath'] : ''); ?>" />
        </div>
    </div>

    <div id="epos_setting">
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <label for="payment_setting_service_provider_code">
                    <?php esc_html_e('service-provider-code', 'express-pay') ?>
                </label>
            </div>
            <div class="col-md-9 col-xs-12">
                <input type="text" id="payment_setting_service_provider_code" name="payment_setting_service_provider_code" placeholder="<?php esc_html_e('enter-service-provider-code', 'express-pay') ?>" value="<?php echo esc_html(isset($param['ServiceProviderCode']) ? $param['ServiceProviderCode'] : ''); ?>" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <label for="payment_setting_service_epos_code">
                    <?php esc_html_e('e-pos-service-code', 'express-pay') ?>
                </label>
            </div>
            <div class="col-md-9 col-xs-12">
                <input type="text" id="payment_setting_service_epos_code" name="payment_setting_service_epos_code" placeholder="<?php esc_html_e('enter-e-pos-service-code', 'express-pay') ?>" value="<?php echo esc_html(isset($param['ServiceEposCode']) ? $param['ServiceEposCode'] : ''); ?>" />
            </div>
        </div>
        <hr />
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_api_url">
                <?php esc_html_e('api-address', 'express-pay') ?>
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_api_url" name="payment_setting_api_url" required placeholder="<?php esc_html_e('enter-api-address', 'express-pay') ?>" value="<?php echo esc_html(isset($param['ApiUrl']) ? $param['ApiUrl'] : ''); ?>" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <label for="payment_setting_sandbox_url">
                <?php esc_html_e('test-api-address', 'express-pay') ?>
            </label>
        </div>
        <div class="col-md-9 col-xs-12">
            <input type="text" id="payment_setting_sandbox_url" name="payment_setting_sandbox_url" required placeholder="<?php esc_html_e('enter-test-api-address', 'express-pay') ?>" value="<?php echo esc_html(isset($param['SandboxUrl']) ? $param['SandboxUrl'] : ''); ?>" />
        </div>
    </div>


    <div class="row">
        <div class="col-md-offset-5 col-md-7">
            <input class="button-blue button-action" type="submit" value="<?php esc_html_e('save', 'express-pay') ?>">
            <input class="button-orange button-action" style="margin-left: 4px;" type="button" onclick="window.location.href='<?php echo esc_html($url . '?page=payment-settings-list'); ?>'" value="<?php esc_html_e('cancel', 'express-pay') ?>">
        </div>
    </div>

</form>
