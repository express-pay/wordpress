<?php
class ExpressPayInvoicesAndPayemnts
{
    /**
     * 
     * Рендеринг страницы счетов
     * 
     */
    static function get_invoices_page()
    {
        ExpressPay::view('admin/admin_header');

        global $wpdb;

        $response = $wpdb->get_results("SELECT id, amount, datecreated, status, options, options_id, dateofpayment FROM " . $wpdb->prefix . "expresspay_invoices");

        if (count($response) == 0) {
            ExpressPay::view(
                'admin/admin_list_invoices_emty',
                array(
                    'url' => get_option('expresspay_plugin_ult')
                )
            );
        } else {
            ExpressPay::view(
                'admin/admin_list_invoices',
                array(
                    'response' => $response,
                    'url' => get_option('expresspay_plugin_ult')
                )
            );
        }

        ExpressPay::view('admin/admin_footer');
    }
}
