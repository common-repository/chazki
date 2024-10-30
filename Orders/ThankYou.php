<?php
namespace Ecomerciar\Chazki\Orders;

use Ecomerciar\Chazki\Helper\Helper;

defined('ABSPATH') || exit;

/**
 * WooCommerce "ThankYou_Order" Main Class
 */
class ThankYou {

      /**
       * Adds Chazki Tracking Info to ThanksYou page
       *
       * @return bool
       */
		public static function add_tracking($thankyoutext, $order) {

			$settings = Helper::get_setup_from_settings();
			$shipping_methods = $order->get_shipping_methods();
			$shipping_method = array_shift($shipping_methods);
			$added_text = '';			
			if ($shipping_method->get_method_id() === 'chazki') {
				if (!empty($shipping_method->get_meta('chazki_tracking_number'))) {
					if (!empty($settings['tracking-form-page'])) {
						$tracking_page = get_page($settings['tracking-form-page']);
							$url = esc_url(get_page_link($tracking_page));
					}
					
					$added_text =  '<p>'
								. __('Seguimiento Chazki', 'chazki')
								. ' : '
								. "<a href='" . $url . "?chazkitrackid=" . $shipping_method->get_meta('chazki_tracking_number') . "'>" 
								. $shipping_method->get_meta('chazki_tracking_number') 
								. "</a>"
								. '</p>';					
				}
			}
			
			$thankyoutext .= $added_text ;
			return $thankyoutext ;
		}

      

}
