<style>
.home-page .wrap-menu ul li a:before {
    display: inline-block;
    content: '';
    width: 63px;
    height: 62px;
    background: url("<?php echo esc_html(plugins_url('img/client_icons_main.png',__FILE__));?>") 0 0 no-repeat;
    float: left;
    margin: 0 22px 0 0
}
</style>
<div class="container">
    <div class="row header">
        <div class="col-md-4">
            <img src="<?php echo esc_html(plugins_url ('img/logo.png',__FILE__));?>" alt="exspress-pay.by" title="express-pay.by" width="216" height="55">
        </div>
        <div class="col-md-8">
            <h2 class="text-center">Сервис «Экспресс Платежи»</h2>
        </div>
    </div>
    <div class="row navbar">
        <div class="col-md-1">
            <a href="#" class="current">Главная</a>
        </div>
        <div class="col-md-2">
            <a href="<?php echo esc_html($url.'&view=invoices');?>">Счета и платежи</a>
        </div>
        <div class="col-md-3">
            <a href="<?php echo esc_html($url.'&view=payment_setting');?>">Настройка методов оплаты</a>
        </div>
        <div class="col-md-6"></div>
    </div>
    <div class="home-page">
        <div class="wrap-menu">
            <ul>
                <li class="menu_2"><a href="<?php echo esc_html($url.'&view=invoices');?>"><div class="text">Счета и<br/> платежи</div></a></li>
                <li class="menu_3"><a href="<?php echo esc_html($url.'&view=payment_setting');?>"><div class="text">Настройки<br /> методов<br/> оплаты</div></a></li>
            </ul>
        </div>
    </div>
</div>
<div class="container footer">
    <div class="wrap-lm" style="display: block;  float: none; margin: 0;">
        <p style="text-align:center;">© Все права защищены | ООО «ТриИнком», <?php echo esc_html(date("Y"));?> | <a href="https://express-pay.by/" target="_blank">express-pay.by</a></p>
    </div>
</div>