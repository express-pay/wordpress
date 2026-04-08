<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="expresspay-payment">
    <div class="expresspay-payment-header">
    <div class="logo">
            <img src="<?php echo esc_html(plugins_url ('admin/img/logo.png',__FILE__));?>" alt="exspress-pay.by" title="express-pay.by" width="216" height="55">
        </div>
        <div class="desc">
            <p><?php esc_html_e('payment-using-the-express-payments-service', 'express-pay') ?></p>
        </div>
    </div>
    <div class="row">
        <p><?php esc_html_e('no-payment-methods-found', 'express-pay') ?></p>
    </div>
</div>
