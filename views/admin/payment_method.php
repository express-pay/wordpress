<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<style>
    i.icon,
    a.icon {
        background-image: url("<?php echo esc_html(plugins_url('img/icons_grid15x.png', __FILE__)); ?>") !important;
        background-position: 0 0;
        background-repeat: no-repeat;
        height: 22px;
        width: 24px;
        display: inline-block;
        cursor: pointer;
        position: relative;
        margin-right: 10px;
    }
</style>
<div class="row navbar">
    <div class="col-md-2">
        <a href="<?php echo esc_html('?page=expresspay-payment'); ?>"><?php esc_html_e('home', 'express-pay') ?></a>
    </div>
    <div class="col-md-2">
        <a href="<?php echo esc_html($url . '?page=invoices-and-payments'); ?>"><?php esc_html_e('invoices-and-payments', 'express-pay') ?></a>
    </div>
    <div class="col-md-2">
        <a href="#" class="current"><?php esc_html_e('settings', 'express-pay') ?></a>
    </div>
    <div class="col-md-2">
        <a target="_blank" href="<?php echo esc_html('https://express-pay.by/extensions/wordpress/erip'); ?>"><?php esc_html_e('help', 'express-pay') ?></a>
    </div>
    <div class="col-md-6"></div>
</div>
<div class="back_link">
    <a href="#" onclick="window.history.back()"><?php esc_html_e('back', 'express-pay') ?></a>
</div>
<div class="add_pay_method_link">
    <a href="<?php echo esc_html($url . '?page=payment-settings&id=0'); ?>"><?php esc_html_e('add-a-payment-method', 'express-pay') ?></a>
</div>
<div class="row">
    <div class="header-table col-md-12">
        <div class="col-md-3"><?php esc_html_e('name', 'express-pay') ?></div>
        <div class="col-md-3"><?php esc_html_e('type-of', 'express-pay') ?></div>
        <div class="col-md-2"><?php esc_html_e('status', 'express-pay') ?></div>
        <div class="col-md-4"><?php esc_html_e('options', 'express-pay') ?></div>
    </div>
    <div class="content col-md-12" style="text-align: center;">
        <?php foreach ($response as $expresspay_row) : ?>
            <div class="table-row">
                <div class="col-md-3"><?php echo esc_html($expresspay_row->name); ?></div>
                <div class="col-md-3"><?php 
                switch ($expresspay_row->type):
                    case 'erip':
                        esc_html_e('erip', 'express-pay');
                    break;
                    case 'card':
                        esc_html_e('internet-acquiring', 'express-pay');
                    break;
                    case 'epos':
                        esc_html_e('epos', 'express-pay');
                    break;
                    endswitch; ?></div>
                <div class="col-md-2">
                    <?php if ($expresspay_row->isactive == 1) : ?>
                        <p class="active"><?php esc_html_e('active', 'express-pay') ?></p>
                    <?php else : ?>
                        <p class="diactive"><?php esc_html_e('disable', 'express-pay') ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                        <a class="icon icon_edit" title="<?php esc_html_e('edit', 'express-pay') ?>" href="?page=payment-settings&id=<?php echo esc_html($expresspay_row->id); ?>"></a>
                    <?php if ($expresspay_row->isactive == 1) :?>
                        <a class="icon icon_stop" onclick="paymentMethodOptions('payment_setting_off', <?php echo esc_html($expresspay_row->id); ?>, '<?php echo esc_attr(wp_create_nonce('express_pay_settings_list')); ?>')" title="<?php esc_html_e('disable', 'express-pay') ?>"></a>
                    <?php else : ?>
                        <a class="icon icon_on"  onclick="paymentMethodOptions('payment_setting_on', <?php echo esc_html($expresspay_row->id); ?>, '<?php echo esc_attr(wp_create_nonce('express_pay_settings_list')); ?>')" title="<?php esc_html_e('enable', 'express-pay') ?>"></a>
                    <?php endif; ?>
                    <a class="icon icon_delete"  onclick="paymentMethodOptions('payment_setting_delete', <?php echo esc_html($expresspay_row->id); ?>, '<?php echo esc_attr(wp_create_nonce('express_pay_settings_list')); ?>')" title="<?php esc_html_e('delete', 'express-pay') ?>"></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
