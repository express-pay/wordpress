<?php

class ExpressPayPaymentSettingsList
{
    /**
     * Получение из БД настроек интеграции. 
     * Рендеринг страницы настроек платежных методов.
     */
    static function get_payment_setting_list_page()
    {
        ExpressPay::view('admin/admin_header');

        global $wpdb;

        $response = $wpdb->get_results("SELECT id, name, type, isactive FROM " . $wpdb->prefix . "expresspay_options");

        if (count($response) == 0) {
            ExpressPay::view(
                'admin/payment_method_empty',
                array(
                    'url' => get_option('expresspay_plugin_ult')
                )
            );
        } else {
            ExpressPay::view(
                'admin/payment_method',
                array(
                    'response' => $response,
                    'url' => get_option('expresspay_plugin_ult'),
                    'ajax_url' => admin_url('admin-ajax.php')
                )
            );
        }

        ExpressPay::view('admin/admin_footer');
    }

    static function payment_setting_options()
    {
        // Check if user is administrator
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized access.', 'express-pay'));
        }

        // Check nonce for CSRF protection
        if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'express_pay_settings_list')) {
            wp_die(__('Security check failed.', 'express-pay'));
        }

        // Verify method parameter exists
        if (!isset($_GET['method'])) {
            return;
        }

        $method = sanitize_text_field($_GET['method']);
        
        switch ($method) {
            case 'payment_setting_on':
                // Verify id parameter exists and is numeric
                if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                    return;
                }

                global $wpdb;
                $table_name = $wpdb->prefix . "expresspay_options";
                $id = intval($_GET['id']);

                $wpdb->update(
                    $table_name,
                    array('isactive' => 1),
                    array('id' => $id),
                    array('%d'),
                    array('%d')
                );
                break;

            case 'payment_setting_off':
                // Verify id parameter exists and is numeric
                if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                    return;
                }

                global $wpdb;
                $table_name = $wpdb->prefix . "expresspay_options";
                $id = intval($_GET['id']);

                $wpdb->update(
                    $table_name,
                    array('isactive' => 0),
                    array('id' => $id),
                    array('%d'),
                    array('%d')
                );
                break;

            case 'payment_setting_delete':
                // Verify id parameter exists and is numeric
                if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                    return;
                }

                global $wpdb;
                $table_name = $wpdb->prefix . "expresspay_options";
                $id = intval($_GET['id']);

                $wpdb->delete($table_name, array('id' => $id), array('%d'));
                break;

            default:
                // Validate REQUEST_URI before storing
                if (isset($_SERVER['REQUEST_URI'])) {
                    $request_uri = esc_url_raw($_SERVER['REQUEST_URI']);
                    update_option('expresspay_plugin_ult', $request_uri);
                }
                break;
        }
    }
}
