<?php


class ExpressPayPayment
{

    /**
     * Рендеринг шорткода
     */
    static function payment_callback($atts, $content = null)
    {
        global $wpdb;

        $response = $wpdb->get_results("SELECT id, name, type, options, isactive FROM " . $wpdb->prefix . "expresspay_options WHERE isactive = 1");

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
    static function get_form_data()
    {
        // Verify all required parameters exist
        if (!isset($_REQUEST['type_id']) || !isset($_REQUEST['amount']) || 
            !isset($_REQUEST['last_name']) || !isset($_REQUEST['first_name']) ||
            !isset($_REQUEST['patronymic']) || !isset($_REQUEST['email']) ||
            !isset($_REQUEST['phone']) || !isset($_REQUEST['url']) || 
            !isset($_REQUEST['info'])) {
            wp_die(__('Missing required parameters.', 'express-pay'));
        }

        $type_id = sanitize_text_field($_REQUEST['type_id']);

        global $wpdb;

        $query = $wpdb->prepare("SELECT id, name, type, options, isactive FROM " . EXPRESSPAY_TABLE_PAYMENT_METHOD_NAME . " WHERE id = %d", $type_id);
        $response = $wpdb->get_row($query);

        if (empty($response)) {
            wp_die(__('Payment method not found.', 'express-pay'));
        }

        if ($response->isactive == 1) {
            $max_id = $wpdb->get_row("SELECT max(id) as id FROM " . $wpdb->prefix . "expresspay_invoices");

            $account_no = $max_id->id == null ? 1 : $max_id->id + 1;

            $options = json_decode($response->options);

            $amount = sanitize_text_field($_REQUEST['amount']);
            $last_name = sanitize_text_field($_REQUEST['last_name']);
            $first_name = sanitize_text_field($_REQUEST['first_name']);
            $patronymic = sanitize_text_field($_REQUEST['patronymic']);
            $email = sanitize_email($_REQUEST['email']);
            $phone = sanitize_text_field($_REQUEST['phone']);
            $url = esc_url_raw($_REQUEST['url']);
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
                    'options' => wp_json_encode($signatureParams),
                    'options_id' => $type_id
                ),
                array('%s', '%d', '%s', '%d', '%s', '%d')
            );

            unset($signatureParams['Token']);

            echo wp_json_encode($signatureParams);
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
        // Verify all required parameters exist
        if (!isset($_REQUEST['type_id']) || !isset($_REQUEST['signature']) || 
            !isset($_REQUEST['account_no']) || !isset($_REQUEST['invoice_no'])) {
            wp_die(__('Missing required parameters.', 'express-pay'));
        }

        $type_id = sanitize_text_field($_REQUEST['type_id']);
        $signature = sanitize_text_field($_REQUEST['signature']);
        $account_no = sanitize_text_field($_REQUEST['account_no']);
        $invoice_no = sanitize_text_field($_REQUEST['invoice_no']);

        global $wpdb;

        $query = $wpdb->prepare("SELECT options FROM " . EXPRESSPAY_TABLE_PAYMENT_METHOD_NAME . " WHERE id = %d", $type_id);
        $response = $wpdb->get_row($query);

        if (empty($response)) {
            wp_die(__('Payment method not found.', 'express-pay'));
        }

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
                    $message_success = __('invoice-successfully-paid', 'express-pay');
                    break;
            }

            $callback["status"] = "success";
            $callback["options"] = $options;
            $callback["message"] = $message_success;
        } else {
            $callback["status"] = "fail";
            $callback["message"] = __('billing-error-message', 'express-pay');
        }

        echo wp_json_encode($callback);

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

        $message_success_erip = __('erip-payment-success-message', 'express-pay');

        $message_success_erip = str_replace("##order_id##", $account_no, $message_success_erip);
        $message_success_erip = str_replace("##erip_path##", $options->EripPath, $message_success_erip);

        if ($options->ShowQrCode) {
            $message_success_erip = $message_success_erip . "<td style=\"text-align: center;padding: 40px 20px 0 0;vertical-align: middle\">
            <br/>##OR_CODE##<br/><p><b>##OR_CODE_DESCRIPTION##</b></p></td></tr></tbody></table>";
            $qr_code = ExpressPay::getQrCode($options->Token, $invoice_no, $options->SecretWord);
            $message_success_erip = str_replace('##OR_CODE##', '<img src="data:image/jpeg;base64,' . $qr_code . '"  width="200" height="200"/>',  $message_success_erip);
            $message_success_erip = str_replace('##OR_CODE_DESCRIPTION##', __('scan-the-qr-code-to-pay', 'express-pay'),  $message_success_erip);
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

        $message_success_epos = __('epos-payment-success-message', 'express-pay');
        $epos_code  = $options->ServiceProviderCode . "-";
        $epos_code .= $options->ServiceEposCode . "-";
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
            // Verify required parameters exist
            if (!isset($_REQUEST['type_id'])) {
                wp_die(__('Missing type_id parameter.', 'express-pay'));
            }

            $data = (isset($_REQUEST['Data'])) ? sanitize_text_field($_REQUEST['Data']) : '';
            $data = stripcslashes($data);
            $signature = (isset($_REQUEST['Signature'])) ? sanitize_text_field($_REQUEST['Signature']) : '';

            $type_id = sanitize_text_field($_REQUEST['type_id']);

            global $wpdb;

            $query = $wpdb->prepare("SELECT id, name, type, options, isactive FROM " . EXPRESSPAY_TABLE_PAYMENT_METHOD_NAME . " WHERE id = %d", $type_id);
            $payment_options = $wpdb->get_row($query);
            
            if (empty($payment_options)) {
                wp_die(__('Payment method not found.', 'express-pay'));
            }

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
                // Validate AccountNo as integer
                $account_no = isset($data->AccountNo) ? intval($data->AccountNo) : 0;
                
                if ($account_no > 0) {
                    switch ($data->CmdType) {
                        case '1':
                            ExpressPay::updateInvoiceStatus($account_no, 3);
                            break;
                        case '2':
                            ExpressPay::updateInvoiceStatus($account_no, 5);
                            break;
                        case '3':
                            $status = isset($data->Status) ? intval($data->Status) : 0;
                            if ($status >= 0) {
                                ExpressPay::updateInvoiceStatus($account_no, $status);
                            }
                            break;
                    }
                }
            }

            if (isset($data->Created)) {
                $account_no = isset($data->AccountNo) ? intval($data->AccountNo) : 0;
                $date_of_payment = isset($data->Created) ? sanitize_text_field($data->Created) : '';
                
                if ($account_no > 0 && !empty($date_of_payment)) {
                    ExpressPay::updateInvoiceDateOfPayment($account_no, $date_of_payment);
                }
            }
        }

        wp_die();
    }
}
