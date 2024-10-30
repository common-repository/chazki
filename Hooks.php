<?php

defined('ABSPATH') || exit;

// --- Shipment Method
add_filter( 'woocommerce_shipping_methods', [ 'WCChazki', 'add_shipping_method' ] );

// --- Order section
add_action( 'woocommerce_order_status_changed', ['\Ecomerciar\Chazki\Orders\Processor', 'handle_order_status'], 10, 4 );
add_action( 'add_meta_boxes', ['\Ecomerciar\Chazki\Orders\Metabox', 'create'] );
add_action( 'woocommerce_order_actions' ,  ['\Ecomerciar\Chazki\Orders\Processor', 'add_order_action'] );
add_action( 'woocommerce_order_action_wc_chazki_order_action', ['\Ecomerciar\Chazki\Orders\Processor', 'handle_order_action'] );

// --- Tracking
add_shortcode( 'chazki-tracking-form', ['\Ecomerciar\Chazki\Orders\TrackingShortcode', 'output']);
add_filter( 'woocommerce_my_account_my_orders_columns', ['\Ecomerciar\Chazki\Orders\OrderList', 'add_tracking_column'] );
add_action( 'woocommerce_my_account_my_orders_column_wc-chazki-tracking',  ['\Ecomerciar\Chazki\Orders\OrderList', 'fill_tracking_column']);

// --- Webhook
add_action('woocommerce_api_wc-chazki-orders', ['\Ecomerciar\Chazki\Orders\Webhooks', 'listener']);

// --- Settings
add_filter( 'plugin_action_links_' . plugin_basename(WCChazki::MAIN_FILE), ['WCChazki', 'create_settings_link']);
add_filter( 'woocommerce_get_sections_shipping' , ['\Ecomerciar\Chazki\Settings\Main', 'add_tab_settings'] );
add_filter( 'woocommerce_get_settings_shipping' , ['\Ecomerciar\Chazki\Settings\Main', 'get_tab_settings'] , 10, 2 );
add_action( 'woocommerce_update_options_chazki_shipping_options', ['\Ecomerciar\Chazki\Settings\Main', 'update_settings']  );

add_filter( 'woocommerce_thankyou_order_received_text', ['\Ecomerciar\Chazki\Orders\ThankYou', 'add_tracking'] , 10 , 2); 

add_filter( 'safe_style_css', function( $styles ) {
    $styles[] = 'display';
    return $styles;
} );

?>
