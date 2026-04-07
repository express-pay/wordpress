<?php

class ExpressPayPaymentSettings
{
    /**
     * 
     * Рендеринг страницы добавление/редактирования метода оплаты.
     * 
     */
    static function get_payment_setting_page()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            check_admin_referer('expresspay_payment_settings_nonce', 'expresspay_nonce');
            self::save_settings();
        }

        ExpressPay::view('admin/admin_header', array('header' => __('settings', 'express-pay')));

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($id == 0) {
            $param = array(
                'ApiUrl' => 'https://api.express-pay.by/v1/',
                'SandboxUrl' => 'https://sandbox-api.express-pay.by/v1/',
            );
        } else {
            global $wpdb;

            $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "expresspay_options WHERE id = %d", $id);
            $response = $wpdb->get_row($query);
            
            if (!$response) {
                wp_die('Settings not found', 'Error', array('response' => 404));
            }

            $param = json_decode($response->options, true);
            if (!is_array($param)) {
                $param = array();
            }
        }

        $url = get_option('expresspay_plugin_ult');

        ExpressPay::view('admin/admin_payment_settings', array(
            'param' => $param,
            'id' => $id,
            'notif_url' => $id == 0 ? __('to-receive-add-a-payment-method', 'express-pay') : admin_url('admin-ajax.php') . '?action=expresspay_receive_notification&type_id=' . intval($id),
            'url' => $url,
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('expresspay_payment_settings_nonce')
        ));

        ExpressPay::view('admin/admin_footer');
    }

    /**
     * 
     * Добавление/изменение настроек метода оплаты в БД.
     * 
     */
    static function save_settings()
    {
        $url = get_option('expresspay_plugin_ult');

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        $params = array(
            'Name' => isset($_POST['payment_setting_name']) ? sanitize_text_field($_POST['payment_setting_name']) : '',
            'Type' => isset($_POST['payment_setting_type']) ? sanitize_text_field($_POST['payment_setting_type']) : '',
            'Token' => isset($_POST['payment_setting_token']) ? sanitize_text_field($_POST['payment_setting_token']) : '',
            'ServiceId' => isset($_POST['payment_setting_service_id']) ? sanitize_text_field($_POST['payment_setting_service_id']) : '',
            'SecretWord' => isset($_POST['payment_setting_secret_word']) ? sanitize_text_field($_POST['payment_setting_secret_word']) : '',
            'SecretWordForNotification' => isset($_POST['payment_setting_secret_word_for_notification']) ? sanitize_text_field($_POST['payment_setting_secret_word_for_notification']) : '',
            'ApiUrl' => isset($_POST['payment_setting_api_url']) ? esc_url_raw($_POST['payment_setting_api_url']) : '',
            'SandboxUrl' => isset($_POST['payment_setting_sandbox_url']) ? esc_url_raw($_POST['payment_setting_sandbox_url']) : '',
            'EripPath' => isset($_POST['payment_setting_erip_path']) ? sanitize_text_field($_POST['payment_setting_erip_path']) : '',
        );

        if (isset($_POST['payment_setting_test_mode']))
            $params['TestMode'] = intval($_POST['payment_setting_test_mode']) ? 1 : 0;

        if (isset($_POST['payment_setting_use_signature_for_notification']))
            $params['UseSignatureForNotification'] = intval($_POST['payment_setting_use_signature_for_notification']) ? 1 : 0;

        switch ($params['Type']) {
            case 'erip':
                if (isset($_POST['payment_setting_show_qr_code']))
                    $params['ShowQrCode'] = intval($_POST['payment_setting_show_qr_code']) ? 1 : 0;

                if (isset($_POST['payment_setting_can_change_name']))
                    $params['CanChangeName'] = intval($_POST['payment_setting_can_change_name']) ? 1 : 0;

                if (isset($_POST['payment_setting_can_change_address']))
                    $params['CanChangeAddress'] = intval($_POST['payment_setting_can_change_address']) ? 1 : 0;

                if (isset($_POST['payment_setting_can_change_amount']))
                    $params['CanChangeAmount'] = intval($_POST['payment_setting_can_change_amount']) ? 1 : 0;

                if (isset($_POST['payment_setting_send_email_notification']))
                    $params['SendEmail'] = intval($_POST['payment_setting_send_email_notification']) ? 1 : 0;

                if (isset($_POST['payment_setting_send_sms_notification']))
                    $params['SendSms'] = intval($_POST['payment_setting_send_sms_notification']) ? 1 : 0;

                break;
            case 'card':
                break;
            case 'epos':

                if (isset($_POST['payment_setting_show_qr_code']))
                    $params['ShowQrCode'] = intval($_POST['payment_setting_show_qr_code']) ? 1 : 0;

                if (isset($_POST['payment_setting_can_change_name']))
                    $params['CanChangeName'] = intval($_POST['payment_setting_can_change_name']) ? 1 : 0;

                if (isset($_POST['payment_setting_can_change_address']))
                    $params['CanChangeAddress'] = intval($_POST['payment_setting_can_change_address']) ? 1 : 0;

                if (isset($_POST['payment_setting_can_change_amount']))
                    $params['CanChangeAmount'] = intval($_POST['payment_setting_can_change_amount']) ? 1 : 0;

                if (isset($_POST['payment_setting_send_email_notification']))
                    $params['SendEmail'] = intval($_POST['payment_setting_send_email_notification']) ? 1 : 0;

                if (isset($_POST['payment_setting_send_sms_notification']))
                    $params['SendSms'] = intval($_POST['payment_setting_send_sms_notification']) ? 1 : 0;

                $params['ServiceProviderCode'] = isset($_POST['payment_setting_service_provider_code']) ? sanitize_text_field($_POST['payment_setting_service_provider_code']) : '';
                $params['ServiceEposCode'] = isset($_POST['payment_setting_service_epos_code']) ? sanitize_text_field($_POST['payment_setting_service_epos_code']) : '';
                break;
        }
        global $wpdb;

        $json = wp_json_encode($params);

        if ($id == 0) {
            $wpdb->insert(
                $wpdb->prefix . "expresspay_options",
                array('name' => $params['Name'], 'type' => $params['Type'], 'options' => $json, 'isactive' => 1),
                array('%s', '%s', '%s', '%d')
            );
        } else {
            $wpdb->update(
                $wpdb->prefix . "expresspay_options",
                array('name' => $params['Name'], 'type' => $params['Type'], 'options' => $json),
                array('id' => $id),
                array('%s', '%s', '%s'),
                array('%d')
            );
        }
?>
        <script>
            window.location.href = '<?php echo esc_html($url); ?>?page=payment-settings-list';
        </script>
<?php
    }

    /**
     * 
     * Получение и парсинг тестовых настроек.
     * 
     */
    static function get_test_mode_params()
    {
        $json = file_get_contents(plugins_url('config/test_settings.json', __FILE__));

        echo $json;

        wp_die();
    }
}
