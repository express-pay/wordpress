<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="expresspay-payment" id="expresspay-payment">
    <div class="expresspay-payment-header">
        <div class="logo">
            <img src="<?php echo esc_html(plugins_url('admin/img/logo.png', __FILE__)); ?>" alt="exspress-pay.by" title="express-pay.by">
        </div>
        <div class="desc">
            <p><?php esc_html_e('payment-using-the-express-payments-service-with-quotes', 'express-pay') ?></p>
        </div>
    </div>
    <div class="row error_panel">
        <p></p>
    </div>
    <input type="hidden" id="ajax-url" value="<?php echo esc_html($ajax_url); ?>" />
    <input type="hidden" id="expresspay-get-form-data-nonce" value="<?php echo esc_attr(wp_create_nonce('expresspay_get_form_data')); ?>" />
    <input type="hidden" id="expresspay-check-invoice-nonce" value="<?php echo esc_attr(wp_create_nonce('expresspay_check_invoice')); ?>" />
    <div class="first_step" id="first_step">

        <?php foreach ($response as $expresspay_row) : ?>
        <?php $expresspay_options = json_decode($expresspay_row->options); ?>
            <div class="row">
                <div class="expresspay_payment_method">
                    <input type="radio" id="payment_method_<?php echo esc_html($expresspay_row->id); ?>" 
                    data-type="<?php echo esc_html($expresspay_row->type); ?>" 
                    data-sendsms="<?php echo esc_html($expresspay_options->SendSms); ?>" data-sendemail="<?php echo esc_html($expresspay_options->SendEmail); ?>"
                    name="payment_method" value="<?php echo esc_html($expresspay_row->id); ?>" />
                    <label for="payment_method_<?php echo esc_html($expresspay_row->id); ?>"><?php echo esc_html($expresspay_row->name); ?></label>
                </div>
            </div>
        <?php endforeach;?>
        <div class="row">
            <div class="label">
                <label for="expresspay-payment-sum"> <?php esc_html_e('amount', 'express-pay') ?></label>
            </div>
            
            <div class="field">
                <?php if (isset($atts['amount'])) : ?>
                    <input type="text" value="<?php echo esc_html($atts['amount']); ?>" <?php echo isset($atts['edit_amount']) && $atts['edit_amount']=='true' ?"":"disabled";?> id="expresspay-payment-sum" placeholder="<?php esc_html_e('enter-amount', 'express-pay') ?>" />
                <?php else: ?>
                    <input type="text" id="expresspay-payment-sum" placeholder="<?php esc_html_e('enter-amount', 'express-pay') ?>" />
                <?php endif ?>
            </div>
        </div>
        <div class="row">
            <button class="confirm_btn" id="btn_step_first"><?php esc_html_e('further', 'express-pay') ?></button>
        </div>
    </div>
    <div class="secont_step" id="second_step">
        
        <div class="row">
            <div class="label">
                <label for="expresspay-payment-purpose"><?php esc_html_e('purpose-of-payment', 'express-pay') ?></label>
            </div>
            <div class="field">
                <?php if (isset($atts['info'])) : ?>
                    <input type="text" value="<?php echo esc_html($atts['info']); ?>" disabled id="expresspay-payment-purpose" placeholder="<?php esc_html_e('enter-purpose-of-payment', 'express-pay') ?>" />
                <?php else: ?>   
                    <input type="text" id="expresspay-payment-purpose" placeholder="<?php esc_html_e('enter-purpose-of-payment', 'express-pay') ?>" />
                <?php endif ?>   
            </div>
        </div>    
        <div class="fio-section" id='fio-section'>
            <div class="row">
                <div class="label">
                    <label for="expresspay-payment-last-name"><?php esc_html_e('surname', 'express-pay') ?></label>
                </div>
                <div class="field">
                    <input type="text" id="expresspay-payment-last-name" placeholder="<?php esc_html_e('enter-surname', 'express-pay') ?>" />
                </div>
            </div>
            <div class="row">
                <div class="label">
                    <label for="expresspay-payment-name"><?php esc_html_e('name', 'express-pay') ?></label>
                </div>
                <div class="field">
                    <input type="text" id="expresspay-payment-name" placeholder="<?php esc_html_e('enter-name', 'express-pay') ?>" />
                </div>
            </div>
            <div class="row">
                <div class="label">
                    <label for="expresspay-payment-secondname"><?php esc_html_e('second-name', 'express-pay') ?></label>
                </div>
                <div class="field">
                    <input type="text" id="expresspay-payment-secondname" placeholder="<?php esc_html_e('enter-second-name', 'express-pay') ?>" />
                </div>
            </div>
        </div>
        <div class="row" id="expresspay-payment-email-container" style="display:none">
            <div class="label">
                <label for="expresspay-payment-email"><?php esc_html_e('e-mail', 'express-pay') ?></label>
            </div>
            <div class="field">
                <input type="text" id="expresspay-payment-email" placeholder="<?php esc_html_e('enter-e-mail', 'express-pay') ?>" />
            </div>
        </div>
        <div class="row" id="expresspay-payment-phone-container" style="display:none">
            <div class="label">
                <label for="expresspay-payment-phone"><?php esc_html_e('mobile-number', 'express-pay') ?></label>
            </div>
            <div class="field">
                <input type="text" id="expresspay-payment-phone" placeholder="<?php esc_html_e('enter-mobile-number', 'express-pay') ?>" />
            </div>
        </div>
        <div class="row">
            <button class="confirm_btn" id="back_second_step"><?php esc_html_e('back', 'express-pay') ?></button>
            <button class="confirm_btn" id="btn_second_step"><?php esc_html_e('checkout', 'express-pay') ?></button>
        </div>
    </div>
    <div class="three_step" id="three_step">      
        <div class="table">
            <div class="row type">
                <div class="desc">
                    <p><?php esc_html_e('payment-method', 'express-pay') ?></p>
                </div>
                <div class="val"></div>
            </div>
            <div class="row amount">
                <div class="desc">
                    <p><?php esc_html_e('amount', 'express-pay') ?></p>
                </div>
                <div class="val"></div>
            </div>
        </div>
        <form class="expresspay-payment-form" method="POST" id="expresspay-payment-form">
            <input type="hidden" name="ServiceId" id="expresspay-payment-service-id" value="" />
            <input type="hidden" name="AccountNo" id="expresspay-payment-account-no" value="" />
            <input type="hidden" name="Amount" id="expresspay-payment-amount" value="" />
            <input type="hidden" name="Currency" id="expresspay-payment-currency" value="" />
            <input type="hidden" name="Info" id="expresspay-payment-info" value="" />
            <input type="hidden" name="Surname" id="expresspay-payment-surname" value="" />
            <input type="hidden" name="FirstName" id="expresspay-payment-first-name" value="" />
            <input type="hidden" name="Patronymic" id="expresspay-payment-patronymic" value="" />
            <input type="hidden" name="IsNameEditable" id="expresspay-payment-is-name-editable" value="" />
            <input type="hidden" name="IsAddressEditable" id="expresspay-payment-is-address-editable" value="" />
            <input type="hidden" name="IsAmountEditable" id="expresspay-payment-is-amount-editable" value="" />
            <input type="hidden" name="EmailNotification" id="expresspay-payment-email-notification" value="" />
            <input type="hidden" name="ReturnType" id="expresspay-payment-return-type" value="redirect" />
            <input type="hidden" name="ReturnUrl" id="expresspay-payment-return-url" value="" />
            <input type="hidden" name="FailUrl" id="expresspay-payment-fail-url" value="" />
            <input type="hidden" name="SmsPhone" id="expresspay-payment-sms-phone" value="" />
            <input type="hidden" name="Signature" id="expresspay-payment-signature" value="" />

            <div class="row">
                <p id="service_message"><?php esc_html_e('wait-second', 'express-pay') ?></p>
            </div>

            <div class="row">	
			<button class="confirm_btn back_link" id="back_three_step"><?php esc_html_e('back', 'express-pay') ?></button>    
                <input class="confirm_btn" style="visibility:hidden" type="submit" id="expresspay-payment-submit-btn" value="<?php esc_html_e('pay', 'express-pay') ?>" />
            </div>
        </form>
    </div>
    <div class="response_step" id="response_step">
        <div class="row">
            <p id="service_response_message"><?php esc_html_e('wait-second', 'express-pay') ?></p>
            <p id="response_message"></p>
        </div>
        <div class="row">
            <button class="btn confirm_btn" id="replay_btn"><?php esc_html_e('repeat', 'express-pay') ?></button>
        </div>
    </div>
</div>
