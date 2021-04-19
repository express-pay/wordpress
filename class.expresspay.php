<?php

class ExpressPay
{

    public static function view( $name, array $args = array() ) {
		
		foreach ( $args AS $key => $val ) {
			$$key = $val;
		}
		
		$file = EXPRESSPAY__PLUGIN_DIR . 'views/'. $name . '.php';

		include( $file );
	}

	public static function computeSignature($signatureParams, $secretWord, $method) {
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
                                    "isamounteditable"),
            "get-details-invoice" => array(
                                    "token",
                                    "id"),
            "cancel-invoice" => array(
                                    "token",
                                    "id"),
            "status-invoice" => array(
                                    "token",
                                    "id"),
            "get-list-invoices" => array(
                                    "token",
                                    "from",
                                    "to",
                                    "accountno",
                                    "status"),
            "get-list-payments" => array(
                                    "token",
                                    "from",
                                    "to",
                                    "accountno"),
            "get-details-payment" => array(
                                    "token",
                                    "id"),
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
                                    "expirationdate"),
           "card-invoice-form"  =>  array(
                                    "token",
                                    "cardinvoiceno"),
            "status-card-invoice" => array(
                                    "token",
                                    "cardinvoiceno",
                                    "language"),
            "reverse-card-invoice" => array(
                                    "token",
                                    "cardinvoiceno"),
            "get-qr-code"          => array(
                                    "token",
                                    "invoiceid",
                                    "viewtype",
                                    "imagewidth",
                                    "imageheight"),
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
                                    "failurl"),
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
									"returntype"),
			"response-web-invoice" => array(
									"token",
									"expresspayaccountnumber",
                                    "expresspayinvoiceno"),
            "notification"         => array(
                                    "data")
        );
        $apiMethod = $mapping[$method];
        $result = "";
        foreach ($apiMethod as $item){
            $result .= $normalizedParams[$item];
        }
        $hash = strtoupper(hash_hmac('sha1', $result, $secretWord));
        return $hash;
    }

    public static function updateInvoiceStatus($account_no, $status )
    {
        global $wpdb;

        $wpdb->update( EXPRESSPAY_TABLE_INVOICES_NAME,
                        array( 'status' => $status),
                        array( 'id' => $account_no ),
                        array( '%d'),
                        array( '%s' )
                    );
    }

    public static function updateInvoiceDateOfPayment($account_no, $dateofpayment )
    {
        global $wpdb;

        $wpdb->update( EXPRESSPAY_TABLE_INVOICES_NAME,
                        array( 'dateofpayment' => $dateofpayment),
                        array( 'id' => $account_no ),
                        array( '%s'),
                        array( '%s' )
                    );
    }

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
        $response = wp_remote_get($url.'?'.$request_params);

        $response = json_decode($response['body']);

        return $response->QrCodeBody;
    }

}