<div class="expresspay-payment" id="expresspay-payment" style="max-width: 80%;">
    <div class="expresspay-payment-header">
    <div class="logo">
            <img src="<?php echo esc_html(plugins_url('admin/img/logo.png',__FILE__));?>" alt="exspress-pay.by" title="express-pay.by" width="216" height="55">
        </div>
        <div class="desc">
            <p>Оплата с помощью сериса Экспресс платежи</p>
        </div>
    </div>
    <div class="row error_panel">
        <p></p>
    </div>
    <input type="hidden" id="ajax-url" value="<?php echo esc_html($ajax_url);?>" />
    <div class="first_step" id="first_step">
        <?php foreach($response as $row): ?>
        <div class="row">
            <div class="expresspay_payment_method">
                <input type="radio" id="payment_method_<?php echo esc_html($row->id);?>" data-type="<?php echo esc_html($row->type);?>" name="payment_method" value="<?php echo esc_html($row->id);?>" />
                <label for="payment_method_<?php echo esc_html($row->id);?>"><?php echo esc_html($row->name);?></label>
            </div>
        </div>
        <?php 
                endforeach; 
            ?>
        <div class="row">
            <div class="label">
                <label for="expresspay-payment-sum">Сумма</label>
            </div>
            <div class="field">
                <input type="text" id="expresspay-payment-sum" placeholder="Введите сумму" />
            </div>
        </div>
        <div class="row">
            <button class="confirm_btn" id="btn_step_first">Далее</button>
        </div>
    </div>
    <div class="secont_step" id="second_step">
        <div class="row back_link">
            <i id="back_second_step">назад</a>
        </div>
        <div class="fio-section" >
            <div class="row">
                <div class="label">
                    <label for="expresspay-payment-last-name">Фамилия</label>
                </div>
                <div class="field">
                    <input type="text" id="expresspay-payment-last-name" placeholder="Введите фамилию" />
                </div>
            </div>
            <div class="row">
                <div class="label">
                    <label for="expresspay-payment-name">Имя</label>
                </div>
                <div class="field">
                    <input type="text" id="expresspay-payment-name" placeholder="Введите имя" />
                </div>
            </div>
            <div class="row">
                <div class="label">
                    <label for="expresspay-payment-secondname">Отчество</label>
                </div>
                <div class="field">
                    <input type="text" id="expresspay-payment-secondname" placeholder="Введите отчество" />
                </div>
            </div>
            <div class="row">
                <div class="label">
                    <label for="expresspay-payment-email">Email</label>
                </div>
                <div class="field">
                    <input type="text" id="expresspay-payment-email" placeholder="Введите email" />
                </div>
            </div>
            <div class="row">
                <div class="label">
                    <label for="expresspay-payment-phone">Моб. телефон</label>
                </div>
                <div class="field">
                    <input type="text" id="expresspay-payment-phone" placeholder="Введите моб. телефон" />
                </div>
            </div>
        </div>
        <div class="row">
            <button id="btn_second_step">Оформить</button>
        </div>
    </div>
    <div class="three_step" id="three_step">
        <div class="row back_link">
            <i id="back_three_step">назад</a>
        </div>
        <div class="table">
            <div class="row type">
                <div class="desc"><p>Метод оплаты</p></div>
                <div class="val"></div>
            </div>
            <div class="row amount">
                <div class="desc"><p>Сумма</p></div>
                <div class="val"></div>
            </div>
        </div>
        <form class="expresspay-payment-form" method="POST" id="expresspay-payment-form">
            <input type="hidden" name="ServiceId" id="expresspay-payment-service-id" value="" />
            <input type="hidden" name="AccountNo" id="expresspay-payment-account-no" value="" />
            <input type="hidden" name="Amount"    id="expresspay-payment-amount" value="" />
            <input type="hidden" name="Currency"  id="expresspay-payment-currency" value="" />
            <input type="hidden" name="Info"      id="expresspay-payment-info" value="" />
            <input type="hidden" name="Surname"   id="expresspay-payment-surname" value="" />
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
                <p id="service_message">Секунду...</p>
                <input style="visibility:hidden" type="submit" id="expresspay-payment-submit-btn" value="Оплатить" /> 
            </div>
        </form>
    </div>
    <div class="response_step" id="response_step">
        <div class="row">
            <p id="service_response_message">Секунду...</p>   
            <p id="response_message"></p>
        </div>
        <div class="row">
            <button class="btn" id="replay_btn">Повторить</button>
        </div>
    </div>
</div>