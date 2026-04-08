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

        $cache_key = 'expresspay_all_invoices';
        $response = wp_cache_get($cache_key);
        
        if (false === $response) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table access via $wpdb is required.
            $response = $wpdb->get_results(
                "SELECT id, amount, datecreated, status, options, options_id, dateofpayment FROM " . $wpdb->prefix . "expresspay_invoices"
            );
            if (!empty($response)) {
                wp_cache_set($cache_key, $response, '', 3600);
            }
        }

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
