<?php
namespace Ecomerciar\Chazki\Orders;

use Ecomerciar\Chazki\Helper\Helper;

defined('ABSPATH') || exit;

/**
 * WooCommerce "My Orders" List's Main Class
 */
class OrderList {

      /**
       * Adds new Column for Chazki Tracking Info
       *
       * @return bool
       */
				public static function add_tracking_column($columns) {
								$columns['chazki-tracking'] = __('Seguimiento Chazki', 'chazki');
								return $columns;
				}

        /**
         * Adds new Column Info for Chazki Tracking Info
         *
         * @return bool
         */
				public static function fill_tracking_column($order) {
								$settings = Helper::get_setup_from_settings();
								$shipping_methods = $order->get_shipping_methods();
								$shipping_method = array_shift($shipping_methods);

								if ($shipping_method->get_method_id() === 'chazki') {
												if (!empty($shipping_method->get_meta('chazki_tracking_number'))) {
																if (!empty($settings['tracking-form-page'])) {
																				$tracking_page = get_page($settings['tracking-form-page']);
																				$url = get_page_link($tracking_page);
																}

																echo "<a href='" . esc_url( $url . "?chazkitrackid=" . $shipping_method->get_meta('chazki_tracking_number') ) . "'>" . esc_html( $shipping_method->get_meta('chazki_tracking_number') ). "</a>";
												}
								}
				}

}
