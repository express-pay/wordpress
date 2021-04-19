<?php

class ExpressPayAdmin
{
    
    static function add_options_page()
    {
        add_options_page("Экспресс Платежи", "Экспресс Платежи", 'manage_options', __FILE__, array('ExpressPayAdmin','options_page'));
    }
    //Страница настроек для плагина
    static function options_page() 
    {
        $url = get_option('expresspay_plugin_ult');

        switch(sanitize_text_field($_GET['view']))
        {
            case 'payment_setting':
                do_action('expresspay_admin_setting');
            break;
            case 'invoices':
                do_action('expresspay_admin_invoices');
            break;
            case 'payment_setting_save':
                if($_SERVER['REQUEST_METHOD'] == 'GET')
                {
                    do_action('expresspay_admin_payment_setting_save', array('id' => sanitize_text_field($_GET['id'])));
                }
                else if($_SERVER['REQUEST_METHOD'] == 'POST')
                {
                    $params = array(
                        'Name' => sanitize_text_field($_REQUEST['payment_setting_name']),
                        'Type' => sanitize_text_field($_REQUEST['payment_setting_type']),
                        'Token' => sanitize_text_field($_REQUEST['payment_setting_token']),
                        'ServiceId' => sanitize_text_field($_REQUEST['payment_setting_service_id']),
                        'SecretWord' => sanitize_text_field($_REQUEST['payment_setting_secret_word']),
                        'UseSignatureForNotification' => sanitize_text_field($_REQUEST['payment_setting_use_signature_for_notification']) ? 1 : 0,
                        'SecretWordForNotification' => sanitize_text_field($_REQUEST['payment_setting_secret_word_for_notification']),
                        'ApiUrl' => sanitize_text_field($_REQUEST['payment_setting_api_url']),
                        'SandboxUrl' => sanitize_text_field($_REQUEST['payment_setting_sandbox_url']),
                        'SuccessMessage' => sanitize_text_field($_REQUEST['payment_setting_success_message']),
                        'FailMessage' => sanitize_text_field($_REQUEST['payment_setting_success_message']),
                        'TestMode' => sanitize_text_field($_REQUEST['payment_setting_test_mode'])? 1 : 0
                    );

                    switch($params['Type'])
                    {
                        case 'ЕРИП':
                            $params['ShowQrCode'] = sanitize_text_field($_REQUEST['payment_setting_show_qr_code']) ? 1 : 0;
                            $params['CanChangeName'] = sanitize_text_field($_REQUEST['payment_setting_can_change_name']) ? 1 : 0;
                            $params['CanChangeAddress'] = sanitize_text_field($_REQUEST['payment_setting_can_change_address']) ? 1 : 0;
                            $params['CanChangeAmount'] = sanitize_text_field($_REQUEST['payment_setting_can_change_amount']) ? 1 : 0;
                            $params['SendEmail'] = sanitize_text_field($_REQUEST['payment_setting_send_email_notification']) ? 1 : 0;
                            $params['SendSms'] = sanitize_text_field($_REQUEST['payment_setting_send_sms_notification']) ? 1 : 0;
                        break;
                        case 'Интернет-эквайринг':break;
                        case 'E-POS':
                            $params['CanChangeName'] = sanitize_text_field($_REQUEST['payment_setting_can_change_name']) ? 1 : 0;
                            $params['CanChangeAddress'] = sanitize_text_field($_REQUEST['payment_setting_can_change_address']) ? 1 : 0;
                            $params['CanChangeAmount'] = sanitize_text_field($_REQUEST['payment_setting_can_change_amount']) ? 1 : 0;
                            $params['SendEmail'] = sanitize_text_field($_REQUEST['payment_setting_send_email_notification']) ? 1 : 0;
                            $params['SendSms'] = sanitize_text_field($_REQUEST['payment_setting_send_sms_notification']) ? 1 : 0;
                            $params['ServiceProviderCode'] = sanitize_text_field($_REQUEST['payment_setting_service_provider_code']);
                            $params['ServiceEposCode'] = sanitize_text_field($_REQUEST['payment_setting_service_epos_code']);
                        break;
                    }
                    global $wpdb;

                    $table_name = $wpdb->prefix . "expresspay_options"; 

                    $json = json_encode($params);

                    $id = sanitize_text_field($_REQUEST['id']);

                    if($id == 0)
                    {
                        $wpdb->insert(
                            $table_name,
                            array( 'name' => $params['Name'], 'type' => $params['Type'], 'options' => $json, 'isactive' => 1 ),
                            array( '%s', '%s', '%s', '%d' )
                        );
                    }
                    else
                    {
                        $wpdb->update( $table_name,
                            array( 'name' => $params['Name'], 'type' => $params['Type'], 'options' => $json ),
                            array( 'id' => $id ),
                            array( '%s', '%s', '%s' ),
                            array( '%d' )
                        );

                    }


                    ?>
                    <script>window.location.href = '<?php echo esc_html($url);?>&view=payment_setting';</script>
                    <?php
                }
            break;
            case 'payment_setting_on':
                global $wpdb;

                $table_name = $wpdb->prefix . "expresspay_options"; 

                $wpdb->update( $table_name,
                            array( 'isactive' => 1 ),
                            array( 'id' => sanitize_text_field($_REQUEST['id'])),
                            array( '%d' ),
                            array( '%d' )
                        );

                ?>
                <script>window.location.href = '<?php echo esc_html($url);?>&view=payment_setting';</script>
                <?php
            break;
            case 'payment_setting_off':
                global $wpdb;

                $table_name = $wpdb->prefix . "expresspay_options"; 

                $wpdb->update( $table_name,
                            array( 'isactive' => 0 ),
                            array( 'id' => sanitize_text_field($_REQUEST['id'] )),
                            array( '%d' ),
                            array( '%d' )
                        );

                ?>
                <script>window.location.href = '<?php echo esc_html($url);?>&view=payment_setting';</script>
                <?php
            break;
            case 'payment_setting_delete':
                global $wpdb;

                $table_name = $wpdb->prefix . "expresspay_options"; 

                $wpdb->delete( $table_name, array( 'id'=> sanitize_text_field($_REQUEST['id'])), array( '%d' ) );

                ?>
                <script>window.location.href = '<?php echo esc_html($url);?>&view=payment_setting';</script>
                <?php
            break;
            default: 
                update_option('expresspay_plugin_ult', $_SERVER['REQUEST_URI']);
                do_action('expresspay_admin_default');
            break;
        }
    }

    static function head_html()
    {
        wp_enqueue_style( 'pluginAdminCssEp', plugins_url('css/styles.css', __FILE__));
        wp_enqueue_style( 'pluginAdminCssBst', plugins_url('css/bootstrap.min.css', __FILE__));
        wp_enqueue_style( 'pluginAdminCss', plugins_url('css/admin.css', __FILE__));
        wp_enqueue_script('pluginAdminJsJsd', plugins_url('js/popper.min.js', __FILE__));
        wp_enqueue_script('pluginAdminJsBst', plugins_url('js/bootstrap.min.js', __FILE__));
    }

    static function get_default_option_page()
    {
        ExpressPay::view('admin/home_options', array('url' => get_option('expresspay_plugin_ult')));
    }

    static function get_payment_setting_page($params = null)
    {
        ExpressPay::view('admin/admin_header');

        global $wpdb;

        $table_name = $wpdb->prefix . "expresspay_options";

        $response = $wpdb->get_results( "SELECT id, name, type, isactive FROM $table_name" );

        if(count($response) == 0)
        {
            ExpressPay::view('admin/payment_method_empty', array('url' => get_option('expresspay_plugin_ult'))); 
        }
        else
        {
            ExpressPay::view('admin/payment_method', array('response' => $response, 'url' => get_option('expresspay_plugin_ult'), 'ajax_url' => admin_url('admin-ajax.php')));
        }
                    
        ExpressPay::view('admin/admin_footer');
    }

    static function get_invoices_page()
    {
        ExpressPay::view('admin/admin_header');

        global $wpdb;

        $table_name = EXPRESSPAY_TABLE_INVOICES_NAME;

        $response = $wpdb->get_results( "SELECT id, amount, datecreated, status, options, options_id, dateofpayment FROM $table_name" );

        if(count($response) == 0)
        {
            ExpressPay::view('admin/payment_method_empty', array('url' => get_option('expresspay_plugin_ult'))); 
        }
        else
        {
            ExpressPay::view('admin/admin_list_invoices', array('response' => $response, 'url' => get_option('expresspay_plugin_ult')));
        }

        ExpressPay::view('admin/admin_footer');
    }

    static function get_payment_setting_save_page($params = null)
    {
        ExpressPay::view('admin/admin_header', array('header' => 'Настройки методов оплаты'));

        $id = $params['id'];

        if($id == 0)
        {
            $param = array(
                'ApiUrl' => 'https://api.express-pay.by/v1/',
                'SandboxUrl' => 'https://sandbox-api.express-pay.by/v1/',
            );
        }
        else
        {
            global $wpdb;

            $table_name = $wpdb->prefix . "expresspay_options";
            $response = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $id");

            $param = json_decode($response->options, true);
        }

        $url = get_option('expresspay_plugin_ult');
        
        wp_enqueue_script('pluginAdminJs', plugins_url('js/admin.js', __FILE__));
        ExpressPay::view('admin/admin_payment_methods_save', array('param' => $param,'notif_url' => $id == 0 ? 'Для получения, добавьте метод оплаты' : admin_url('admin-ajax.php') . '?action=receive_notification&type_id=' . $id, 'url' => $url, 'ajax_url' => admin_url('admin-ajax.php')));

        ExpressPay::view('admin/admin_footer');
    }

    static function get_test_mode_params()
    {
        $json = file_get_contents(plugins_url('config/test_settings.json', __FILE__)); 

        echo $json;

        wp_die();

    }
}