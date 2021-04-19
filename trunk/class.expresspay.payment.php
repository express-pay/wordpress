<?php


class ExpressPayPayment
{
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
    static function plugin_deactivation()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "expresspay_options";

        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);

        $invoices_table_name = EXPRESSPAY_TABLE_INVOICES_NAME;
        $sql = "DROP TABLE IF EXISTS $invoices_table_name";
        $wpdb->query($sql);

        delete_option('expresspay_plugin_is_active', 0);
        delete_option('expresspay_plugin_ult', '');
    }

    static function plugin_uninstall()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "expresspay_options";

        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);

        $invoices_table_name = EXPRESSPAY_TABLE_INVOICES_NAME;
        $sql = "DROP TABLE IF EXISTS $invoices_table_name";
        $wpdb->query($sql);

        delete_option('expresspay_plugin_is_active', 0);
        delete_option('expresspay_plugin_ult', '');
    }

    static function head_html()
    {
        wp_enqueue_style( 'pluginPaymentCss', plugins_url('css/payment.css', __FILE__));
        wp_enqueue_script('pluginPaymentJs', plugins_url('js/shortcode.js', __FILE__));
    }

    static function payment_callback($atts, $content = null)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "expresspay_options";

        $response = $wpdb->get_results("SELECT id, name, type, isactive FROM $table_name where isactive = 1");

        ob_start();

        if (count($response) == 0) {
            ExpressPay::view("payment_method_empty", array('response' => $response));
        } else {
            ExpressPay::view("payment_form", array('response' => $response, 'ajax_url' => admin_url('admin-ajax.php')));
        }

        return ob_get_clean();
    }

    static function get_form_gata()
    {
        $type_id = sanitize_text_field($_REQUEST['type_id']);

        global $wpdb;

        $response = $wpdb->get_row("SELECT id, name, type, options, isactive FROM " . EXPRESSPAY_TABLE_PAYMENT_METHOD_NAME . " WHERE id = $type_id");

        if ($response->isactive == 1) {
            $max_id = $wpdb->get_row("SELECT max(id) as id FROM " . EXPRESSPAY_TABLE_INVOICES_NAME);

            $account_no = $max_id->id == null ? 1 : $max_id->id + 1;

            $options = json_decode($response->options);

            $amount = sanitize_text_field($_REQUEST['amount']);
            $last_name = sanitize_text_field($_REQUEST['last_name']);
            $first_name = sanitize_text_field($_REQUEST['first_name']);
            $patronymic = sanitize_text_field($_REQUEST['patronymic']);
            $email = sanitize_email($_REQUEST['email']);
            $phone = sanitize_text_field($_REQUEST['phone']);
            $url = sanitize_text_field($_REQUEST['url']);

            $signatureParams = array(
                "Token" => $options->Token,
                "ServiceId" => $options->ServiceId,
                "AccountNo" => $account_no,
                "Amount" => $amount,
                "Currency" => 933,
                "Info" => 'тест',
                "ReturnType" => "redirect",
                "ReturnUrl" => $url . "?type_id=$type_id",
                "FailUrl" => $url . "?type_id=$type_id",
                "Action" => $options->TestMode == 1 ? $options->SandboxUrl : $options->ApiUrl
            );

            if ($response->type == 'Интернет-эквайринг') {
                $signatureParams["Action"] .=   "web_cardinvoices";

                $signatureParams['Signature'] = ExpressPay::computeSignature($signatureParams, $options->SecretWord, 'add-webcard-invoice');
            } else {
                $signatureParams["Surname"] = $last_name;
                $signatureParams["FirstName"] = $first_name;
                $signatureParams["Patronymic"] = $patronymic;
                $signatureParams["IsNameEditable"] = $options->CanChangeName;
                $signatureParams["IsAddressEditable"] = $options->CanChangeAddress;
                $signatureParams["IsAmountEditable"] = $options->CanChangeAmount;
                $signatureParams["EmailNotification"] = $email;
                $signatureParams["SmsPhone"] = $phone;
                $signatureParams["Action"] .=   "web_invoices";
                $signatureParams['Signature'] = ExpressPay::computeSignature($signatureParams, $options->SecretWord, 'add-web-invoice');
            }

            $wpdb->insert(
                EXPRESSPAY_TABLE_INVOICES_NAME,
                array(
                    'id' => $account_no, 'amount' => $amount,
                    'datecreated' => current_time('mysql', 1),
                    'status' => 0,
                    'options' => json_encode($signatureParams),
                    'options_id' => $type_id
                ),
                array('%s', '%d', '%s', '%d', '%s', '%d')
            );

            unset($signatureParams['Token']);

            echo json_encode($signatureParams);
        } else {
        }
        //echo json_encode($response);
        wp_die();
    }

   static function check_invoice()
    {
        $type_id = sanitize_text_field($_REQUEST['type_id']);
        $signature = sanitize_text_field($_REQUEST['Signature']);
        $account_no = sanitize_text_field($_REQUEST['ExpressPayAccountNumber']);
        $invoice_no = sanitize_text_field($_REQUEST['ExpressPayInvoiceNo']);

        global $wpdb;

        $response = $wpdb->get_row("SELECT options FROM " . EXPRESSPAY_TABLE_PAYMENT_METHOD_NAME . " WHERE id = $type_id");

        $options = json_decode($response->options);
        $signatureParams = array(
            "token" => $options->Token,
            "expresspayaccountnumber" => $account_no,
            "expresspayinvoiceno" => $invoice_no
        );

        $valid_signature = ExpressPay::computeSignature($signatureParams, $options->SecretWord, "response-web-invoice");


        $callback = array();

        if ($signature == $valid_signature) {

            $wpdb->update(
                EXPRESSPAY_TABLE_INVOICES_NAME,
                array('status' => 1),
                array('ID' => $account_no),
                array('%d'),
                array('%s')
            );

            $message_success_erip = "<h3>Счет добавлен в систему ЕРИП для оплаты </h3><h4>Ваш номер заказа: ##order_id##</h4><table style=\"width: 100%;text-align: left;\"><tbody><tr><td valign=\"top\" style=\"text-align:left;\">Вам необходимо произвести платеж в любой системе, позволяющей проводить оплату через ЕРИП (пункты банковского обслуживания, банкоматы, платежные терминалы, системы интернет-банкинга, клиент-банкинга и т.п.).<br/>1. Для этого в перечне услуг ЕРИП перейдите в раздел:<br/><b>##erip_path##</b><br/>2. Далее введите номер заказа <b>##order_id##</b> и нажмите \"Продолжить\"<br/>3. Проверить корректность информации<br/>4. Совершить платеж.</td><td style=\"text-align: center;padding: 40px 20px 0 0;vertical-align: middle\"><br/>##OR_CODE##<br/><p><b>##OR_CODE_DESCRIPTION##</b></p></td></tr></tbody></table>";
					
            $message_success_erip = str_replace("##order_id##", $account_no, nl2br($message_success_erip, true));
            $message_success_erip = str_replace("##erip_path##", $options->SuccessMessage, nl2br($message_success_erip, true));

            if ($options->ShowQrCode) {
                $qr_code = ExpressPay::getQrCode($options->Token, $invoice_no, $options->SecretWord);
                $message_success_erip = str_replace('##OR_CODE##', '<img src="data:image/jpeg;base64,' . $qr_code . '"  width="200" height="200"/>',  $message_success_erip);
                $message_success_erip = str_replace('##OR_CODE_DESCRIPTION##', 'Отсканируйте QR-код для оплаты',  $message_success_erip);
            } else {
                $message_success_erip = str_replace('##OR_CODE##', '',  $message_success_erip);
                $message_success_erip = str_replace('##OR_CODE_DESCRIPTION##', '',  $message_success_erip);
            }


            $qr_code = ExpressPay::getQrCode($options->Token, $invoice_no, $options->SecretWord);
            $message_success_epos = '<h2>Счет добавлен в систему E-POS для оплаты </h2><h3>Ваш номер заказа: ##epos_code##</h3><table style="width: 100%;text-align: left;"><tbody><tr><td valign="top" style="text-align:left;">Вам необходимо произвести платеж в любой системе, позволяющей проводить оплату через ЕРИП (пункты банковского обслуживания, банкоматы, платежные терминалы, системы интернет-банкинга, клиент-банкинга и т.п.).<br /> 1. Для этого в перечне услуг ЕРИП перейдите в раздел:<b>Система "Расчет" (ЕРИП)-&gt;Сервис E-POS-&gt;E-POS - оплата товаров и услуг</b><br />2. В поле "Код" введите <b>##epos_code##</b> и нажмите "Продолжить"<br />3. Проверить корректность информации<br />4. Совершить платеж.</td><td style="text-align: center;padding: 40px 20px 0 0;vertical-align: middle"><p>##qr_code##</p><p><b>Отсканируйте QR-код для оплаты</b></p></td></tr></tbody></table>';
            $epos_code  = $options->ServiceEposCode . "-";
            $epos_code .= "1-";
            $epos_code .= $account_no;

            $message_success_epos = str_replace("##qr_code##", '<img src="data:image/jpeg;base64,' . $qr_code . '"  width="200" height="200"/>', nl2br($message_success_epos, true));
            $message_success_epos = str_replace("##epos_code##", $epos_code, nl2br($message_success_epos, true));

            $message_success_card = "Счет успешно оплачен!";

            switch ($options->Type) {
                case 'ЕРИП':
                    $message_success = $message_success_erip;
                    break;
                case 'E-POS':
                    $message_success = $message_success_epos;
                    break;
                case 'Интернет-эквайринг': 
                    $message_success = $message_success_card;
                    break;
            }



            $callback["status"] = "success";
            $callback["options"] = $options;
            $callback["message"] = $message_success;
        } else {
            $callback["status"] = "fail";
            $callback["message"] = "Во время процесса выставления счета произошла ошибка";
        }

        echo json_encode($callback);

        wp_die();
    }

    static function receive_notification()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = (isset($_REQUEST['Data'])) ? sanitize_text_field($_REQUEST['Data']) : '';
            $data = stripcslashes($data);
            $signature = (isset($_REQUEST['Signature'])) ? sanitize_text_field($_REQUEST['Signature']) : '';

            $type_id = sanitize_text_field($_REQUEST['type_id']);

            global $wpdb;

            $payment_options = $wpdb->get_row('SELECT id, name, type, options, isactive FROM ' . EXPRESSPAY_TABLE_PAYMENT_METHOD_NAME . ' WHERE id = ' . $type_id);

            $options = json_decode($payment_options->options);

            if ($options->UseSignatureForNotification == 1) {
                $valid_signature = ExpressPay::computeSignature(array("data" => $data), $options->SecretWordForNotification, 'notification');

                if ($valid_signature == $signature) {
                } else {
                    wp_die();
                }
            }

            $data = json_decode($data);


            if (isset($data->CmdType)) {
                switch ($data->CmdType) {
                    case '1':
                        ExpressPay::updateInvoiceStatus($data->AccountNo, 3);
                        break;
                    case '2':
                        ExpressPay::updateInvoiceStatus($data->AccountNo, 5);
                        break;
                    case '3':
                        ExpressPay::updateInvoiceStatus($data->AccountNo, $data->Status);
                        break;
                }
            }

            if (isset($data->Created)) {
                ExpressPay::updateInvoiceDateOfPayment($data->AccountNo, $data->Created);
            }
        }

        wp_die();
    }

    static function get_payment_setting()
    {
        global $wpdb;

        $type_id = sanitize_text_field($_REQUEST['type_id']);

        $payment_options = $wpdb->get_row('SELECT id, name, type, options, isactive FROM ' . EXPRESSPAY_TABLE_PAYMENT_METHOD_NAME . ' WHERE id = ' . $type_id);

        echo $payment_options->options;

        wp_die();
    }
}
