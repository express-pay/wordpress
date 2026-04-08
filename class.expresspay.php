<?php

class ExpressPay
{
    /**
     * 
     * Получение вью из файла.
     * 
     * @param string $name Название файла вью
     * @param array  $args Аргументы для передачи на вью
     * 
     */
    public static function view($name, array $args = array())
    {
        foreach ($args as $key => $val) {
            $$key = $val;
        }

        $file = EXPRESSPAY__PLUGIN_DIR . 'views/' . $name . '.php';

        include($file);
    }

    /**
     * 
     * Подлкючение стилей и скирптов в административной части интеграции
     * 
     */
    static function plugin_admin_styles()
    {
        //CSS
        wp_enqueue_style('pluginAdminCssEp', plugins_url('css/styles.css', __FILE__), array(), expresspay_get_plugin_version());
        wp_enqueue_style('pluginAdminCssBst', plugins_url('css/bootstrap.min.css', __FILE__), array(), expresspay_get_plugin_version());
        wp_enqueue_style('pluginAdminCss', plugins_url('css/admin.css', __FILE__), array(), expresspay_get_plugin_version());
        
        //JS
        wp_enqueue_script('pluginAdminJsJsd', plugins_url('js/popper.min.js', __FILE__), array(), expresspay_get_plugin_version(), true);
        wp_enqueue_script('pluginAdminJsBst', plugins_url('js/bootstrap.min.js', __FILE__), array(), expresspay_get_plugin_version(), true);
        wp_enqueue_script('pluginAdminJs', plugins_url('js/admin.js', __FILE__), array('jquery'), expresspay_get_plugin_version(), true);
    }

    /**
     * 
     * Подключение стилей и скриптов в клиентской части интеграции
     * 
     */
    static function plugin_client_styles()
    {
        //CSS
        wp_enqueue_style('pluginPaymentCss', plugins_url('css/payment.css', __FILE__), array(), expresspay_get_plugin_version());

        //JS
        wp_enqueue_script('pluginPaymentJs', plugins_url('js/shortcode.js', __FILE__), array('jquery'), expresspay_get_plugin_version(), true);
    }

    /**
     * 
     * Формирование цифровой подписи
     * 
     * @param array  $signatureParams Список передаваемых параметров
     * @param string $secretWord      Секретное слово
     * @param string $method          Метод формирования цифровой подписи
     * 
     * @return string $hash           Сформированная цифровая подпись
     * 
     */
    public static function computeSignature($signatureParams, $secretWord, $method)
    {
        $normalizedParams = array_change_key_case($signatureParams, CASE_LOWER);
        $mapping = array(
            "add-invoice" => array(
                "token",
                "accountno",
                "amount",
                "currency",
                "expiration",
                "info",
                "surname",
                "firstname",
                "patronymic",
                "city",
                "street",
                "house",
                "building",
                "apartment",
                "isnameeditable",
                "isaddresseditable",
                "isamounteditable"
            ),
            "get-details-invoice" => array(
                "token",
                "id"
            ),
            "cancel-invoice" => array(
                "token",
                "id"
            ),
            "status-invoice" => array(
                "token",
                "id"
            ),
            "get-list-invoices" => array(
                "token",
                "from",
                "to",
                "accountno",
                "status"
            ),
            "get-list-payments" => array(
                "token",
                "from",
                "to",
                "accountno"
            ),
            "get-details-payment" => array(
                "token",
                "id"
            ),
            "add-card-invoice"  =>  array(
                "token",
                "accountno",
                "expiration",
                "amount",
                "currency",
                "info",
                "returnurl",
                "failurl",
                "language",
                "pageview",
                "sessiontimeoutsecs",
                "expirationdate"
            ),
            "card-invoice-form"  =>  array(
                "token",
                "cardinvoiceno"
            ),
            "status-card-invoice" => array(
                "token",
                "cardinvoiceno",
                "language"
            ),
            "reverse-card-invoice" => array(
                "token",
                "cardinvoiceno"
            ),
            "get-qr-code"          => array(
                "token",
                "invoiceid",
                "viewtype",
                "imagewidth",
                "imageheight"
            ),
            "add-web-invoice"      => array(
                "token",
                "serviceid",
                "accountno",
                "amount",
                "currency",
                "expiration",
                "info",
                "surname",
                "firstname",
                "patronymic",
                "city",
                "street",
                "house",
                "building",
                "apartment",
                "isnameeditable",
                "isaddresseditable",
                "isamounteditable",
                "emailnotification",
                "smsphone",
                "returntype",
                "returnurl",
                "failurl"
            ),
            "add-webcard-invoice" => array(
                "token",
                "serviceid",
                "accountno",
                "expiration",
                "amount",
                "currency",
                "info",
                "returnurl",
                "failurl",
                "language",
                "sessiontimeoutsecs",
                "expirationdate",
                "returntype"
            ),
            "response-web-invoice" => array(
                "token",
                "expresspayaccountnumber",
                "expresspayinvoiceno"
            ),
            "notification"         => array(
                "data"
            )
        );
        $apiMethod = $mapping[$method];
        $result = "";
        foreach ($apiMethod as $item) {
            $result .= $normalizedParams[$item];
        }
        $hash = strtoupper(hash_hmac('sha1', $result, $secretWord));
        return $hash;
    }

    /**
     * 
     * Обновление статуса счета
     * 
     * @param string $account_no Номер счета
     * @param int    $status     Новый статус счета
     * 
     */
    public static function updateInvoiceStatus($account_no, $status)
    {
        global $wpdb;

        // Validate and cast inputs
        $account_no = intval($account_no);
        $status = intval($status);

        if ($account_no <= 0 || $status < 0) {
            return false;
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table write via $wpdb is required.
        $wpdb->update(
            EXPRESSPAY_TABLE_INVOICES_NAME,
            array('status' => $status),
            array('id' => $account_no),
            array('%d'),
            array('%d')
        );
        
        // Invalidate cache after update
        wp_cache_delete('expresspay_all_invoices');
        return true;
    }

    /**
     * 
     * Обновление даты оплаты счета
     * 
     * @param string $account_no        Номер счета
     * @param string $dateofpayment     Дата оплаты счета
     * 
     */
    public static function updateInvoiceDateOfPayment($account_no, $dateofpayment)
    {
        global $wpdb;

        // Validate and cast inputs
        $account_no = intval($account_no);
        $dateofpayment = sanitize_text_field($dateofpayment);

        if ($account_no <= 0 || empty($dateofpayment)) {
            return false;
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table write via $wpdb is required.
        $wpdb->update(
            EXPRESSPAY_TABLE_INVOICES_NAME,
            array('dateofpayment' => $dateofpayment),
            array('id' => $account_no),
            array('%s'),
            array('%d')
        );
        
        // Invalidate cache after update
        wp_cache_delete('expresspay_all_invoices');
        return true;
    }

    /**
     * 
     * Получение Qr-кода
     * 
     * @param string $token      Токен
     * @param int    $invoiceId  Номер счета в сервисе Эксрпесс Платежи
     * @param string $secretWord Секретное слово
     * 
     */
    public static function getQrCode($token, $invoiceId, $secretWord)
    {
        $request_params = array(
            'Token' => $token,
            'InvoiceId' => $invoiceId,
            'ViewType' => 'base64'
        );

        $request_params["Signature"] =  self::computeSignature($request_params, $secretWord, 'get-qr-code');

        $request_params = http_build_query($request_params);

        $url = 'https://api.express-pay.by/v1/qrcode/getqrcode/';
        $response = wp_remote_get($url . '?' . $request_params);

        $response = json_decode($response['body']);

        return $response->QrCodeBody;
    }

    /**
     * 
     * Хук обработки активации плагина
     * 
     */
    static function plugin_activation()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "expresspay_options";

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        type tinytext NOT NULL,
        options text NOT NULL,
        isactive tinyint NOT NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $invoices_table_name = EXPRESSPAY_TABLE_INVOICES_NAME;
        $sql = "CREATE TABLE $invoices_table_name(
        id int NOT NULL UNIQUE,
        amount decimal not null,
        datecreated datetime not null,
        status tinyint not null,
        dateofpayment datetime null,
        options text null,
        options_id int not null,
        PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta($sql);

        add_option('expresspay_plugin_ult', '');
    }

    /**
     * 
     * Хук обработки деактивации плагина
     * 
     */
    static function plugin_deactivation()
    {
        // Deactivation should not remove DB schema/data. Cleanup is handled in uninstall.
        delete_option('expresspay_plugin_is_active');
        delete_option('expresspay_plugin_ult');

        wp_cache_delete('expresspay_active_options');
        wp_cache_delete('expresspay_payment_settings_list');
        wp_cache_delete('expresspay_all_invoices');
        wp_cache_delete('expresspay_max_invoice_id');
    }

    /**
     * 
     * Хук обработки удаления плагина
     * 
     */
    static function plugin_uninstall()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "expresspay_options";
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange -- Uninstall cleanup of plugin tables.
        $wpdb->query("DROP TABLE IF EXISTS " . esc_sql($table_name));

        $invoices_table_name = EXPRESSPAY_TABLE_INVOICES_NAME;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange -- Uninstall cleanup of plugin tables.
        $wpdb->query("DROP TABLE IF EXISTS " . esc_sql($invoices_table_name));

        delete_option('expresspay_plugin_is_active');
        delete_option('expresspay_plugin_ult');
    }

    static function log_error_exception($name, $message, $e)
    {
        self::log($name, "ERROR", $message . '; EXCEPTION MESSAGE - ' . $e->getMessage() . '; EXCEPTION TRACE - ' . $e->getTraceAsString());
    }

    static function log_error($name, $message)
    {
        self::log($name, "ERROR", $message);
    }

    static function log_info($name, $message)
    {
        self::log($name, "INFO", $message);
    }

    static function log($name, $type, $message)
    {
        // Get upload directory
        $upload_dir = wp_upload_dir();
        $log_url = $upload_dir['basedir'] . '/expresspay';

        // Use WordPress Filesystem if needed
        if (!file_exists($log_url)) {
            // Create directory with proper error handling
            if (!wp_mkdir_p($log_url)) {
                // If directory creation fails, don't log
                return;
            }
        }

        $log_file = $log_url . '/express-pay-' . gmdate('Y.m.d') . '.log';

        // Prepare log entry with sanitized values
        $remote_addr = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';
        
        $log_entry = $type . " - IP - " . $remote_addr . "; DATETIME - " . gmdate("Y-m-d H:i:s") . "; USER AGENT - " . $user_agent . "; FUNCTION - " . $name . "; MESSAGE - " . $message . ';' . PHP_EOL;

        file_put_contents($log_file, $log_entry, FILE_APPEND);
    }
}
