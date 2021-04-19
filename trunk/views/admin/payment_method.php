<style>
i.icon,
a.icon{
    background-image: url("<?php echo esc_html(plugins_url('img/icons_grid15x.png',__FILE__));?>") !important;
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
    <div class="col-md-1">
        <a href="<?php echo esc_html($url);?>">Главная</a>
    </div>
    <div class="col-md-2">
        <a href="<?php echo esc_html($url);?>&view=invoices">Счета и платежи</a>
    </div>
    <div class="col-md-3">
        <a href="#" class="current">Настройка методов оплаты</a>
    </div>
    <div class="col-md-6"></div>
</div>
<div class="back_link">
    <a href="#" onclick="window.history.back()">Вернуться назад</a>
</div>
<div class="add_pay_method_link">
        <a href="<?php echo esc_html($url.'&view=payment_setting_save&id=0');?>" class="add-link">Добавить метод оплаты</a>
</div>
<div class="row">
    <div class="header-table col-md-12">
        <div class="col-md-3">Название</div>
        <div class="col-md-3">Тип</div>
        <div class="col-md-2">Статус</div>
        <div class="col-md-4">Опции</div>
    </div>
    <div class="content col-md-12" style="text-align: center;">
    <?php foreach($response as $row): ?>
        <div class="table-row">
            <div class="col-md-3"><?php echo esc_html($row->name);?></div>
            <div class="col-md-3"><?php echo esc_html($row->type);?></div>
            <div class="col-md-2">
                <?php if($row->isactive == 1):?>
                    <p class="active">Активна</p>
                <?php else:?>
                    <p class="diactive">Отключена</p>
                <?php endif;?>
            </div>
            <div class="col-md-4">
                <a class="icon icon_edit" title="Редактировать" href="<?php echo esc_html($url);?>&view=payment_setting_save&id=<?php echo esc_html($row->id);?>"></a>
                <?php
                    if($row->isactive == 1):
                ?>
                    <a class="icon icon_stop" title="Отключить" href="<?php echo esc_html($url);?>&view=payment_setting_off&id=<?php echo esc_html($row->id);?>"></a>
                <?php else:?>
                    <a class="icon icon_on" title="Включить" href="<?php echo esc_html($url);?>&view=payment_setting_on&id=<?php echo esc_html($row->id);?>"></a>
                <?php endif;?>
                <a class="icon icon_delete" title="Удалить" href="<?php echo esc_html($url);?>&view=payment_setting_delete&id=<?php echo esc_html($row->id);?>"></a>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</div>