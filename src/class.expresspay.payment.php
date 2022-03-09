<?php


class ExpressPayPayment
{

    /**
     * Рендеринг шорткода
     */
    static function payment_callback($atts, $content = null)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "expresspay_options";

        $response = $wpdb->get_results("SELECT id, name, type, options, isactive FROM $table_name where isactive = 1");

        ob_start();

        ExpressPay::plugin_client_styles();
        if (count($response) == 0) {
            ExpressPay::view("payment_method_empty", array('response' => $response));
        } else {
            ExpressPay::view("payment_form", array('atts' => $atts, 'response' => $response, 'ajax_url' => admin_url('admin-ajax.php')));
        }

        return ob_get_clean();
    }

    /**
     * Получение данных для заполнения формы
     * 
     * @return json Ответ на клиента
     */
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
            $info = sanitize_text_field($_REQUEST['info']);

            if ($options->SendSms)
            {
                $client_phone = preg_replace('/[^0-9]/', '', $phone);
                $client_phone = substr($client_phone, -9);
                $client_phone = "375$client_phone";
            }


            $signatureParams = array(
                "Token" => $options->Token,
                "ServiceId" => $options->ServiceId,
                "AccountNo" => $account_no,
                "Amount" => $amount,
                "Currency" => 933,
                "Info" => $info,
                "ReturnType" => "redirect",
                "ReturnUrl" => add_query_arg(['type_id' => $type_id, 'result' => 1], $url),
                "FailUrl" => add_query_arg(['type_id' => $type_id, 'result' => 0], $url),
                "Action" => $options->TestMode == 1 ? $options->SandboxUrl : $options->ApiUrl
            );

            if ($response->type == 'card') {
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
                $signatureParams["SmsPhone"] = $client_phone;
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
        wp_die();
    }

    /**
     * Функция обработки ответа от API
     * 
     * @return json Ответ на клиента
     */
    static function check_invoice()
    {
        $type_id = sanitize_text_field($_REQUEST['type_id']);
        $signature = sanitize_text_field($_REQUEST['signature']);
        $account_no = sanitize_text_field($_REQUEST['account_no']);
        $invoice_no = sanitize_text_field($_REQUEST['invoice_no']);

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

            switch ($options->Type) {
                case 'erip':
                    $message_success = self::getEripMessage($options, $invoice_no, $account_no);
                    break;
                case 'epos':
                    $message_success = self::getEposMessage($options, $invoice_no, $account_no);
                    break;
                case 'card':
                    $message_success = __('The invoice has been successfully paid!', 'wordpress_expresspay');
                    break;
            }

            $callback["status"] = "success";
            $callback["options"] = $options;
            $callback["message"] = $message_success;
        } else {
            $callback["status"] = "fail";
            $callback["message"] = __('An error occurred during the billing process', 'wordpress_expresspay');
        }

        echo json_encode($callback);

        wp_die();
    }

    /**
     * Получение информациооного сообщения для способа оплаты ЕРИП
     * 
     * @param object $options    Настройки интеграции
     * @param int    $invoice_no Номер счета сервиса Эксперсс Платежи
     * @param string $account_no Номер счета присвоенный интеграцией
     * 
     * @return string $hash Сформированное сообщение
     */
    static function getEripMessage($options, $invoice_no, $account_no)
    {

        $message_success_erip = __('<h3>Account added to the ERIP system for payment</h3>
        <h4>Your order number: ##order_id##</h4>
        <table style="width: 100%;text-align: left;"><tbody><tr><td valign="top" style="text-align:left;">
        You need to make a payment in any system that allows you to pay through ERIP (items banking services, ATMs,
        payment terminals, Internet banking systems, client banking, etc.).
        <br/> 1. To do this, in the list of ERIP services go to the section:<br/><b>##erip_path##</b>
        <br/> 2. Next, enter the order number <b>##order_id##</b> and click "Continue"
        <br/> 3. Check if the information is correct. 
        <br/> 4. Make a payment.</td> ', 'wordpress_expresspay');

        $message_success_erip = str_replace("##order_id##", $account_no, $message_success_erip);
        $message_success_erip = str_replace("##erip_path##", $options->EripPath, $message_success_erip);

        if ($options->ShowQrCode) {
            $message_success_erip = $message_success_erip . "<td style=\"text-align: center;padding: 40px 20px 0 0;vertical-align: middle\">
            <br/>##OR_CODE##<br/><p><b>##OR_CODE_DESCRIPTION##</b></p></td></tr></tbody></table>";
            $qr_code = ExpressPay::getQrCode($options->Token, $invoice_no, $options->SecretWord);
            $message_success_erip = str_replace('##OR_CODE##', '<img src="data:image/jpeg;base64,' . $qr_code . '"  width="200" height="200"/>',  $message_success_erip);
            $message_success_erip = str_replace('##OR_CODE_DESCRIPTION##', __('Scan the QR code to pay', 'wordpress_expresspay'),  $message_success_erip);
        } else {
            $message_success_erip = str_replace('##OR_CODE##', '',  $message_success_erip);
            $message_success_erip = str_replace('##OR_CODE_DESCRIPTION##', '',  $message_success_erip);
        }

        return $message_success_erip;
    }

    /**
     * Получение информациооного сообщения для способа оплаты E-POS
     * 
     * @param object $options    Настройки интеграции
     * @param int    $invoice_no Номер счета сервиса Эксперсс Платежи
     * @param string $account_no Номер счета присвоенный интеграцией
     * 
     * @return string $hash Сформированное сообщение
     */
    static function getEposMessage($options, $invoice_no, $account_no)
    {
        $qr_code = ExpressPay::getQrCode($options->Token, $invoice_no, $options->SecretWord);

        $message_success_epos = __('<h3>Account added to the E-POS system for payment</h3>
				<h4>Your order number: ##epos_code##</h4>
				<table style="width: 100%;text-align: left;"><tbody><tr><td valign="top" style="text-align:left;">
				You need to make a payment in any system that allows you to pay through ERIP (banking service points, 
				ATMs, payment terminals, Internet banking systems, client banking, etc.).
				<br/> 1. To do this, in the list of ERIP services, go to the section: <b> Settlement System (ERIP)
				 -&gt; E-POS Service-&gt; E-POS - payment for goods and services</b>
				 <br/>2. In the Code field, enter <b>##epos_code##</b> and click "Continue"
				 <br/>3. Check the correctness of the information
				 <br/>4. Make a payment.</td>
				 <td style="text-align: center;padding: 40px 20px 0 0;vertical-align: middle">
				 <p>##qr_code##</p><p><b>Scan the QR code to pay</b></p></td></tr></tbody></table>', 'wordpress_expresspay');
        $epos_code  = $options->ServiceEposCode . "-";
        $epos_code .= "1-";
        $epos_code .= $account_no;

        $message_success_epos = str_replace("##qr_code##", '<img src="data:image/jpeg;base64,' . $qr_code . '"  width="200" height="200"/>', $message_success_epos);
        $message_success_epos = str_replace("##epos_code##", $epos_code, $message_success_epos);

        return $message_success_epos;
    }

    /**
     * Получение и обратока уведомления
     */
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
}
