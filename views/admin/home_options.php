<style>
    .home-page .wrap-menu ul li a:before {
        display: inline-block;
        content: '';
        width: 63px;
        height: 62px;
        background: url("<?php echo esc_html(plugins_url('img/client_icons_main.png', __FILE__)); ?>") 0 0 no-repeat;
        display: inline-block;
        margin: 0 22px 0 0
    }
</style>
<div class="container">
    <div class="row header">
        <div class="col-md-4">
            <img src="<?php echo esc_html(plugins_url('img/logo.png', __FILE__)); ?>" alt="exspress-pay.by" title="express-pay.by" width="216" height="55">
        </div>
        <div class="col-md-8">
            <h2 class="text-center"><?php esc_html_e('service-express-payments', 'express-pay') ?></h2>
        </div>
    </div>
    <div class="row navbar">
        <div class="col-md-2">
            <a href="#" class="current"><?php esc_html_e('home', 'express-pay') ?></a>
        </div>
        <div class="col-md-2">
            <a href="<?php echo esc_html($url . '?page=invoices-and-payments'); ?>"><?php esc_html_e('invoices-and-payments', 'express-pay') ?></a>
        </div>
        <div class="col-md-2">
            <a href="<?php echo esc_html($url . '?page=payment-settings-list'); ?>"><?php esc_html_e('settings', 'express-pay') ?></a>
        </div>
        <div class="col-md-2">
            <a target="_blank" href="<?php echo esc_html('https://express-pay.by/extensions/wordpress/erip'); ?>"><?php esc_html_e('help', 'express-pay') ?></a>
        </div>
        <div class="col-md-6"></div>
    </div>
    <div class="home-page">
        <div class="wrap-menu">
            <ul>
                <li class="menu_2"><a href="<?php echo esc_html($url . '?page=invoices-and-payments'); ?>">
                        <div class="text"><?php esc_html_e('invoices-and-payments', 'express-pay') ?></div>
                    </a></li>
                <li class="menu_3"><a href="<?php echo esc_html($url . '?page=payment-settings-list'); ?>">
                        <div class="text"> <?php esc_html_e('settings', 'express-pay') ?> </div>
                    </a></li>
            </ul>
        </div>
    </div>
</div>
<div class="container footer">
    <div class="wrap-lm" style="display: block;  float: none; margin: 0;">
        <p style="text-align:center;"> <?php esc_html_e('copyright', 'express-pay') ?> <?php echo esc_html(date("Y")); ?> | <a href="https://express-pay.by/" target="_blank">express-pay.by</a></p>
    </div>
</div>
