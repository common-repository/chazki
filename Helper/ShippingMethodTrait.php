<?php
namespace Ecomerciar\Chazki\Helper;

use Ecomerciar\Chazki\ShippingMethod\WC_Chazki;

trait ShippingMethodTrait {

      /**
       * Gets the shipping Service Values from Chazki Setting
       *
       * @param WC_Order $order
       * @return array|false
       */
				public static function get_shipping_service($order) {

								$shipping_methods = $order->get_shipping_methods();
								if (empty($shipping_methods)) {
												return;
								}
								$shipping_method = array_shift($shipping_methods);
								$wc = new WC_Chazki($shipping_method['instance_id']);								
								return $wc->get_service();
				}

				public static function get_shipping_cost($order){
					$shipping_methods = $order->get_shipping_methods();
					if (empty($shipping_methods)) {
									return;
					}
					$shipping_method = array_shift($shipping_methods);
					$wc = new WC_Chazki($shipping_method['instance_id']);								
					return $wc->get_cost();
				}

}
