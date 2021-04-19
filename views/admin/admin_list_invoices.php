<div class="row navbar">
    <div class="col-md-1">
        <a href="<?php echo esc_html($url);?>">Главная</a>
    </div>
    <div class="col-md-2">
        <a href="#" class="current">Счета и платежи</a>
    </div>
    <div class="col-md-3">
        <a href="<?php echo esc_html($url.'&view=payment_setting');?>">Настройка методов оплаты</a>
    </div>
    <div class="col-md-6"></div>
</div>
<div class="back_link">
    <a href="#" onclick="window.history.back()">Вернуться назад</a>
</div>
<div class="row">
    <div class="header-table col-md-12">
        <div class="col-md-2">Номер счета</div>
        <div class="col-md-2">Сумма</div>
        <div class="col-md-2">Дата создания</div>
        <div class="col-md-2">Статус</div>
        <div class="col-md-2">Дата платежа</div>
    </div>
    <div class="content col-md-12" style="text-align: center;">
    <?php foreach($response as $row): ?>
        <div class="row">
            <div class="col-md-2"><?php echo esc_html($row->id);?></div>
            <div class="col-md-2"><?php echo esc_html($row->amount .' BYN');?></div>
            <div class="col-md-2"><?php echo esc_html($row->datecreated);?></div>
            <div class="col-md-2">
                <?php 
                    switch($row->status)
                    {
                        case 0:
                            echo esc_html('В процессе');
                        break;
                        case 1: 
                            echo esc_html('Ожидает оплату');
                        break;
                        case 2: 
                            echo esc_html('Просрочен');
                        break;
                        case 3: 
                            echo esc_html('Оплачен');
                        break;
                        case 4: 
                            echo esc_html('Оплачен частично');
                        break;
                        case 5: 
                            echo esc_html('Отменен');
                        break;
                        case 6:
                            echo esc_html('Оплачен с помощью банковской карты');
                        break;
                    }
                ?>
            </div>
            <div class="col-md-2"><?php echo esc_html($row->dateofpayment);?></div>
            <hr style="color:#888888"/>
        </div>
    <?php endforeach; ?>
    </div>
</div>