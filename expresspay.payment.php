<?php
/*
Plugin Name: ExpressPay Payment
Plugin URI: https://express-pay.by/cms-extensions/wordpress
Description: Place the plugin shortcode at any of your pages and start to accept payments in WordPress instantly
Version: 1.0.0
Author: ООО "ТриИнком"
Author URI: https://express-pay.by
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

global $wpdb;

define('EXPRESSPAY__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define('EXPRESSPAY_TABLE_PAYMENT_METHOD_NAME', $wpdb->prefix . "expresspay_options");
define('EXPRESSPAY_TABLE_INVOICES_NAME', $wpdb->prefix . "expresspay_invoices");

require_once( EXPRESSPAY__PLUGIN_DIR . 'class.expresspay.payment.php' );
require_once( EXPRESSPAY__PLUGIN_DIR . 'class.expresspay.admin.php' );
require_once( EXPRESSPAY__PLUGIN_DIR . 'class.expresspay.php');

register_activation_hook( __FILE__, array( 'ExpressPayPayment', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'ExpressPayPayment', 'plugin_deactivation' ) );
register_uninstall_hook ( __FILE__,array( 'ExpressPayPayment', 'plugin_uninstall' ) );

add_action('admin_menu', array('ExpressPayAdmin', 'add_options_page'));
add_action('admin_enqueue_scripts',  array('ExpressPayAdmin','head_html'));
add_action('wp_head',  array('ExpressPayPayment','head_html'));

add_action('expresspay_admin_setting',array('ExpressPayAdmin','get_payment_setting_page'));
add_action('expresspay_admin_default',array('ExpressPayAdmin','get_default_option_page'));
add_action('expresspay_admin_invoices',array('ExpressPayAdmin','get_invoices_page'));
add_action('expresspay_admin_payment_setting_save',array('ExpressPayAdmin','get_payment_setting_save_page'));

add_shortcode('expresspay_payment',  array( 'ExpressPayPayment', 'payment_callback'));

add_action( 'wp_ajax_receive_notification',      array( 'ExpressPayPayment','receive_notification') ); // For logged in users
add_action( 'wp_ajax_nopriv_receive_notification',   array( 'ExpressPayPayment','receive_notification') ); // For anonymous users

add_action( 'wp_ajax_get_form_gata',         array( 'ExpressPayPayment','get_form_gata') ); // For logged in users
add_action( 'wp_ajax_nopriv_get_form_gata',  array( 'ExpressPayPayment','get_form_gata') ); // For anonymous users

add_action( 'wp_ajax_check_invoice',      array( 'ExpressPayPayment','check_invoice') ); // For logged in users
add_action( 'wp_ajax_nopriv_check_invoice',   array( 'ExpressPayPayment','check_invoice') ); // For anonymous users


add_action( 'wp_ajax_get_test_mode_params',      array( 'ExpressPayAdmin','get_test_mode_params') ); // For logged in users
add_action( 'wp_ajax_nopriv_get_test_mode_params',   array( 'ExpressPayAdmin','get_test_mode_params') ); // For anonymous users

add_action( 'wp_ajax_get_payment_setting',      array( 'ExpressPayPayment','get_payment_setting') ); // For logged in users
add_action( 'wp_ajax_nopriv_get_payment_setting',   array( 'ExpressPayPayment','get_payment_setting') ); // For anonymous users




