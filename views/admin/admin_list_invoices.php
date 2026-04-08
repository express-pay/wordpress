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
        <?php foreach ($response as $expresspay_row) : ?>
            <div class="row">
                <div class="col-md-2"><?php echo esc_html($expresspay_row->id); ?></div>
                <div class="col-md-2"><?php echo esc_html($expresspay_row->amount . ' BYN'); ?></div>
                <div class="col-md-2"><?php echo esc_html($expresspay_row->datecreated); ?></div>
                <div class="col-md-2">
                    <?php
                    switch ($expresspay_row->status) {
                        case 0:
                            esc_html_e('during', 'express-pay');
                            break;
                        case 1:
                            esc_html_e('awaiting-payment', 'express-pay');
                            break;
                        case 2:
                            esc_html_e('expired', 'express-pay');
                            break;
                        case 3:
                            esc_html_e('paid-up', 'express-pay');
                            break;
                        case 4:
                            esc_html_e('paid-in-part', 'express-pay');
                            break;
                        case 5:
                            esc_html_e('canceled', 'express-pay');
                            break;
                        case 6:
                            esc_html_e('paid-with-a-bank-card', 'express-pay');
                            break;
                    }
                    ?>
                </div>
                <div class="col-md-2"><?php echo esc_html($expresspay_row->dateofpayment); ?></div>
                <hr style="color:#888888" />
            </div>
        <?php endforeach; ?>
    </div>
</div>
