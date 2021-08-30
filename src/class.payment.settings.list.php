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

        $table_name = $wpdb->prefix . "expresspay_options";

        $response = $wpdb->get_results("SELECT id, name, type, isactive FROM $table_name");

        $url = get_option('expresspay_plugin_ult');

        if (isset(($_GET['action']))) {
            switch (sanitize_text_field($_GET['action'])) {
                case 'payment_setting_on':
                    global $wpdb;

                    $table_name = $wpdb->prefix . "expresspay_options";

                    $wpdb->update(
                        $table_name,
                        array('isactive' => 1),
                        array('id' => sanitize_text_field($_REQUEST['id'])),
                        array('%d'),
                        array('%d')
                    );

?>
                    <script>
                        window.location.href = '<?php echo esc_html($url); ?>';
                    </script>
                <?php
                    break;
                case 'payment_setting_off':
                    global $wpdb;

                    $table_name = $wpdb->prefix . "expresspay_options";

                    $wpdb->update(
                        $table_name,
                        array('isactive' => 0),
                        array('id' => sanitize_text_field($_REQUEST['id'])),
                        array('%d'),
                        array('%d')
                    );

                ?>
                    <script>
                        window.location.href = '<?php echo esc_html($url); ?>';
                    </script>
                <?php
                    break;
                case 'payment_setting_delete':
                    global $wpdb;

                    $table_name = $wpdb->prefix . "expresspay_options";

                    $wpdb->delete($table_name, array('id' => sanitize_text_field($_REQUEST['id'])), array('%d'));
                ?>
                    <script>
                        window.location.href = '<?php echo esc_html($url); ?>';
                    </script>
<?php
                    break;
                default:
                    update_option('expresspay_plugin_ult', $_SERVER['REQUEST_URI']);
                    break;
            }
        }

        if (count($response) == 0) {
            ExpressPay::view('admin/payment_method_empty', array('url' => get_option('expresspay_plugin_ult')));
        } else {
            ExpressPay::view('admin/payment_method', array('response' => $response, 'url' => get_option('expresspay_plugin_ult'), 'ajax_url' => admin_url('admin-ajax.php')));
        }

        ExpressPay::view('admin/admin_footer');
    }
}
