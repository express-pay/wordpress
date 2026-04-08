<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="row navbar">
    <div class="col-md-2">
        <a href="<?php echo esc_html($url . '?page=expresspay-payment'); ?>"><?php esc_html_e('home', 'express-pay') ?></a>
    </div>
    <div class="col-md-2">
        <a href="#" class="current"><?php esc_html_e('invoices-and-payments', 'express-pay') ?></a>
    </div>
    <div class="col-md-2">
        <a href="<?php echo esc_html($url . '?page=payment-settings-list'); ?>"><?php esc_html_e('settings', 'express-pay') ?></a>
    </div>
    <div class="col-md-2">
        <a target="_blank" href="<?php echo esc_html('https://express-pay.by/extensions/wordpress/erip'); ?>"><?php esc_html_e('help', 'express-pay') ?></a>
    </div>
    <div class="col-md-6"></div>
</div>
<div class="back_link">
    <a href="#" onclick="window.history.back()"><?php esc_html_e('back', 'express-pay') ?></a>
</div>
<div class="row">
    <div class="header-table col-md-12">
        <div class="col-md-2"><?php esc_html_e('account-number', 'express-pay') ?></div>
        <div class="col-md-2"><?php esc_html_e('amount', 'express-pay') ?></div>
        <div class="col-md-2"><?php esc_html_e('date-of-creation', 'express-pay') ?></div>
        <div class="col-md-2"><?php esc_html_e('status', 'express-pay') ?></div>
        <div class="col-md-2"><?php esc_html_e('payment-date', 'express-pay') ?></div>
    </div>
    <div class="content col-md-12" style="text-align: center;">
        <div class="table-row">
            <div class="col-md-12 text-empty-table">
                <p class="text-center"><?php esc_html_e('account-list-is-empty', 'express-pay') ?></p>
            </div>
        </div>
    </div>
</div>
