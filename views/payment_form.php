<div class="expresspay-payment" id="expresspay-payment">
    <div class="expresspay-payment-header">
        <div class="logo">
            <img src="<?php echo esc_html(plugins_url('admin/img/logo.png', __FILE__)); ?>" alt="exspress-pay.by" title="express-pay.by">
        </div>
        <div class="desc">
            <p><?php esc_html_e('Payment using the «Express payments service»', 'wordpress_expresspay') ?></p>
        </div>
    </div>
    <div class="row error_panel">
        <p></p>
    </div>
    <input type="hidden" id="ajax-url" value="<?php echo esc_html($ajax_url); ?>" />
    <div class="first_step" id="first_step">

        <?php foreach ($response as $row) : ?>
        <?php $options = json_decode($row->options); ?>
            <div class="row">
                <div class="expresspay_payment_method">
                    <input type="radio" id="payment_method_<?php echo esc_html($row->id); ?>" 
                    data-type="<?php echo esc_html($row->type); ?>" 
                    data-sendsms="<?php echo esc_html($options->SendSms); ?>" data-sendemail="<?php echo esc_html($options->SendEmail); ?>"
                    name="payment_method" value="<?php echo esc_html($row->id); ?>" />
                    <label for="payment_method_<?php echo esc_html($row->id); ?>"><?php echo esc_html($row->name); ?></label>
                </div>
            </div>
        <?php endforeach;?>
        <div class="row">
            <div class="label">
                <label for="expresspay-payment-sum"> <?php esc_html_e('Amount', 'wordpress_expresspay') ?></label>
            </div>
            
            <div class="field">
                <?php if (isset($atts['amount'])) : ?>
                    <input type="text" value="<?php echo esc_html($atts['amount']); ?>" disabled id="expresspay-payment-sum" placeholder="<?php esc_html_e('Enter amount', 'wordpress_expresspay') ?>" />
                <?php else: ?>   
                    <input type="text" id="expresspay-payment-sum" placeholder="<?php esc_html_e('Enter amount', 'wordpress_expresspay') ?>" />
                <?php endif ?>            
            </div>
        </div>
        <div class="row">
            <button class="confirm_btn" id="btn_step_first"><?php esc_html_e('Further', 'wordpress_expresspay') ?></button>
        </div>
    </div>
    <div class="secont_step" id="second_step">
        
        <div class="row">
            <div class="label">
                <label for="expresspay-payment-purpose"><?php esc_html_e('Purpose of payment', 'wordpress_expresspay') ?></label>
            </div>
            <div class="field">
                <?php if (isset($atts['info'])) : ?>
                    <input type="text" value="<?php echo esc_html($atts['info']); ?>" disabled id="expresspay-payment-purpose" placeholder="<?php esc_html_e('Enter purpose of payment', 'wordpress_expresspay') ?>" />
                <?php else: ?>   
                    <input type="text" id="expresspay-payment-purpose" placeholder="<?php esc_html_e('Enter purpose of payment', 'wordpress_expresspay') ?>" />
                <?php endif ?>   
            </div>
        </div>    
        <div class="fio-section" id='fio-section'>
            <div class="row">
                <div class="label">
                    <label for="expresspay-payment-last-name"><?php esc_html_e('Surname', 'wordpress_expresspay') ?></label>
                </div>
                <div class="field">
                    <input type="text" id="expresspay-payment-last-name" placeholder="<?php esc_html_e('Enter surname', 'wordpress_expresspay') ?>" />
                </div>
            </div>
            <div class="row">
                <div class="label">
                    <label for="expresspay-payment-name"><?php esc_html_e('Name', 'wordpress_expresspay') ?></label>
                </div>
                <div class="field">
                    <input type="text" id="expresspay-payment-name" placeholder="<?php esc_html_e('Enter name', 'wordpress_expresspay') ?>" />
                </div>
            </div>
            <div class="row">
                <div class="label">
                    <label for="expresspay-payment-secondname"><?php esc_html_e('Second name', 'wordpress_expresspay') ?></label>
                </div>
                <div class="field">
                    <input type="text" id="expresspay-payment-secondname" placeholder="<?php esc_html_e('Enter second name', 'wordpress_expresspay') ?>" />
                </div>
            </div>
        </div>
        <div class="row" id="expresspay-payment-email-container" style="display:none">
            <div class="label">
                <label for="expresspay-payment-email"><?php esc_html_e('E-mail', 'wordpress_expresspay') ?></label>
            </div>
            <div class="field">
                <input type="text" id="expresspay-payment-email" placeholder="<?php esc_html_e('Enter e-mail', 'wordpress_expresspay') ?>" />
            </div>
        </div>
        <div class="row" id="expresspay-payment-phone-container" style="display:none">
            <div class="label">
                <label for="expresspay-payment-phone"><?php esc_html_e('Mobile number', 'wordpress_expresspay') ?></label>
            </div>
            <div class="field">
                <input type="text" id="expresspay-payment-phone" placeholder="<?php esc_html_e('Enter mobile number', 'wordpress_expresspay') ?>" />
            </div>
        </div>
        <div class="row">
            <button class="confirm_btn" id="back_second_step"><?php esc_html_e('Back', 'wordpress_expresspay') ?></button>
            <button class="confirm_btn" id="btn_second_step"><?php esc_html_e('Checkout', 'wordpress_expresspay') ?></button>
        </div>
    </div>
    <div class="three_step" id="three_step">      
        <div class="table">
            <div class="row type">
                <div class="desc">
                    <p><?php esc_html_e('Payment method', 'wordpress_expresspay') ?></p>
                </div>
                <div class="val"></div>
            </div>
            <div class="row amount">
                <div class="desc">
                    <p><?php esc_html_e('Amount', 'wordpress_expresspay') ?></p>
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
                <p id="service_message"><?php esc_html_e('Whait second...', 'wordpress_expresspay') ?></p>
            </div>

            <div class="row">	
				<button class="confirm_btn back_link" id="back_three_step"><?php esc_html_e('Back', 'wordpress_expresspay') ?></button>	
                <input class="confirm_btn" style="visibility:hidden" type="submit" id="expresspay-payment-submit-btn" value="<?php esc_html_e('Pay', 'wordpress_expresspay') ?>" />
            </div>
        </form>
    </div>
    <div class="response_step" id="response_step">
        <div class="row">
            <p id="service_response_message"><?php esc_html_e('Whait second...', 'wordpress_expresspay') ?></p>
            <p id="response_message"></p>
        </div>
        <div class="row">
            <button class="btn confirm_btn" id="replay_btn"><?php esc_html_e('Repeat', 'wordpress_expresspay') ?></button>
        </div>
    </div>
</div>