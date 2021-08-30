jQuery(document).ready(function () {
    let expressPayAccountNumber = GetParameterValues('ExpressPayAccountNumber');

    if (expressPayAccountNumber != null) {
        let expressPayInvoiceNo = GetParameterValues('ExpressPayInvoiceNo');
        let signature = GetParameterValues('Signature');
        let type_id = GetParameterValues('type_id');

        checkInvoice(signature, expressPayAccountNumber, expressPayInvoiceNo, type_id);
    } else {

    }

    jQuery('#expresspay-payment-submit-btn').click(function () {
        //getFormData();
    });

    jQuery('#btn_step_first').click(function () {

        let type = jQuery('#first_step input[name=payment_method]:checked').attr('data-type');
        let sum = jQuery('#expresspay-payment-sum').val();

        if (type == undefined || isNaN(sum) || sum < 0.01) {
            return;
        } else if (type == "card") {
            jQuery('#fio-section').hide();
            jQuery('#first_step').hide(350);
            jQuery('#second_step').show(350);

        } else {
            getPaymentSetting();
            jQuery('#first_step').hide(350);
            jQuery('#second_step').show(350);
        }


    });

    jQuery('#back_second_step').click(function () {

        jQuery('#first_step').show(350);
        jQuery('#second_step').hide(350);

    });

    jQuery('#btn_second_step').click(function () {

        setTableValue();
        getFormData();
        jQuery('#second_step').hide(350);
        jQuery('#three_step').show(350);

    });

    jQuery('#back_three_step').click(function () {


        let type = jQuery('input[name=payment_method]:checked').attr('data-type');

        if (type == "card") {
            jQuery('#first_step').show(350);
            jQuery('#three_step').hide(350);
        } else {
            jQuery('#second_step').show(350);
            jQuery('#three_step').hide(350);

        }

    });

    jQuery('#replay_btn').click(function () {
        let url = window.location.href;

        let expressPayAccountNumber = GetParameterValues('ExpressPayAccountNumber');
        let expressPayInvoiceNo = GetParameterValues('ExpressPayInvoiceNo');
        let signature = GetParameterValues('Signature');
        let type_id = GetParameterValues('type_id');

        url = url.substring(0, url.indexOf('type_id') - 1);

        window.location.href = url;
    });

    function setTableValue() {
    
        let type = jQuery("input[type='radio']:checked:last").next().text();

        jQuery('#three_step .table .row.type .val').html(type);

        let amount = jQuery('#expresspay-payment-sum').val();

        jQuery('#three_step .table .row.amount .val').html(amount + " BYN");
    }

    function getFormData() {
        let type_id = jQuery('#first_step input[name=payment_method]:checked').val();
        let amount = jQuery('#expresspay-payment-sum').val();

        let last_name = jQuery('#expresspay-payment-last-name').val();
        let first_name = jQuery('#expresspay-payment-name').val();
        let patronymic = jQuery('#expresspay-payment-secondname').val();
        let info = jQuery('#expresspay-payment-purpose').val();
        let email = jQuery('#expresspay-payment-email').val();
        let phone = jQuery('#expresspay-payment-phone').val();

        let url = jQuery('#ajax-url').val();

        jQuery(function ($) {
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    action: 'get_form_gata',
                    type_id: type_id,
                    amount: amount,
                    last_name: last_name,
                    first_name: first_name,
                    patronymic: patronymic,
                    info: info,
                    email: email,
                    phone: phone,
                    url: window.location.href
                },
                success: function (response) {
                    jQuery('#service_message').css('visibility', 'hidden');
                    jQuery('#expresspay-payment-submit-btn').css('visibility', 'visible');
                    response = $.parseJSON(response);

                    setFormValue(response);
                }
            });
        });
    }

    function setFormValue(options) {
        jQuery('#expresspay-payment-form').attr('action', options.Action);
        jQuery('#expresspay-payment-service-id').val(options.ServiceId);
        jQuery('#expresspay-payment-account-no').val(options.AccountNo);
        jQuery('#expresspay-payment-amount').val(options.Amount);
        jQuery('#expresspay-payment-currency').val(options.Currency);
        jQuery('#expresspay-payment-info').val(options.Info);
        jQuery('#expresspay-payment-surname').val(options.Surname);
        jQuery('#expresspay-payment-first-name').val(options.FirstName);
        jQuery('#expresspay-payment-patronymic').val(options.Patronymic);
        jQuery('#expresspay-payment-is-name-editable').val(options.IsNameEditable);
        jQuery('#expresspay-payment-is-address-editable').val(options.IsAddressEditable);
        jQuery('#expresspay-payment-is-amount-editable').val(options.IsAmountEditable);
        jQuery('#expresspay-payment-email-notification').val(options.EmailNotification);
        jQuery('#expresspay-payment-sms-phone').val(options.SmsPhone);
        jQuery('#expresspay-payment-signature').val(options.Signature);
        jQuery('#expresspay-payment-return-url').val(options.ReturnUrl);
        jQuery('#expresspay-payment-fail-url').val(options.FailUrl);
    }

    function GetParameterValues(param) {
        var url = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for (var i = 0; i < url.length; i++) {
            var urlparam = url[i].split('=');
            if (urlparam[0] == param) {
                return urlparam[1];
            }
        }
    }

    function checkInvoice(signature, account_no, invoice_no, type_id) {
        let url = jQuery('#ajax-url').val();

        jQuery('#first_step').hide(800);
        jQuery('#second_step').hide(800);
        jQuery('#three_step').hide(800);
        jQuery('#response_step').show(350);
        jQuery('#replay_btn').hide(800);

        jQuery(function ($) {
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    action: 'check_invoice',
                    type_id: type_id,
                    signature: signature,
                    account_no: account_no,
                    invoice_no: invoice_no
                },
                success: function (data) {
                    data = $.parseJSON(data);

                    if (data.status == "success") {
                        jQuery('#service_response_message').hide(800);
                        jQuery('#response_message').addClass('success');
                        jQuery('#replay_btn').show(800);
                        jQuery('#replay_btn').html('Продолжить');

                    } else {
                        jQuery('#service_response_message').hide(800);
                        jQuery('#response_message').addClass('fail');
                        jQuery('#replay_btn').show(800);
                        jQuery('#replay_btn').html('Повторить');
                    }
                    jQuery('#response_message').html(data.message);
                }

            });
        });
    }

    function getPaymentSetting() {
        let type_id = jQuery('#first_step input[name=payment_method]:checked').val();

        let type = jQuery('#first_step input[name=payment_method]:checked').attr('data-type');

        if (type == 'card')
            return;

        let url = jQuery('#ajax-url').val();

        jQuery(function ($) {
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    action: 'get_payment_setting',
                    type_id: type_id,
                },
                success: function (response) {

                    response = $.parseJSON(response);

                    setSecondStepFields(response);

                }
            });
        });
    }

    function setSecondStepFields(data) {

        if (data.SendEmail == 1) {
            jQuery('#expresspay-payment-email-container').show(400);
        } 

        if (data.SendSms == 1) {
            jQuery('#expresspay-payment-phone-container').show(400);
        }

    }
});