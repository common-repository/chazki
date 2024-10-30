<?php
namespace Ecomerciar\Chazki\Orders;

use Ecomerciar\Chazki\Sdk\ChazkiSdk;

defined('ABSPATH') || exit;

/**
 * Order Processor's Main Class
 */
class Processor {
	/**
	 * Handles the WooCommerce order status
	 *
	 * @param int $order_id
	 * @param string $status_from
	 * @param string $status_to
	 * @param WC_Order $order
	 * @return void
	 */
	public static function handle_order_status(int $order_id, string $status_from, string $status_to, \WC_Order $order) {
		$shipping_methods = $order->get_shipping_methods();
		if (empty($shipping_methods)) {
			return;
		}
		$shipping_method = array_shift($shipping_methods);
		/*Verify It's Chazki Shipping Methos*/
		if ($shipping_method->get_method_id() === 'chazki') {
			$sdk = new ChazkiSdk();
			/*Verify It's Auto Process Enabled*/
			if ($sdk->is_auto_process()) {
				if ($order->has_status($sdk->get_auto_process_status())) {
					if (empty($shipping_method->get_meta('chazki_tracking_number'))) {
						$response = $sdk->process_order($order);
						if (!$response) {
							$order->add_order_note(__('No fue posible notificar el pedido a Chazki. Contacte al administrador.', 'chazki'));
								return;
						}
						if ($response['ordersWithErrors'] === 0 && $response['success'] === true ) {
							$tracking_id = $response['trackCode'];
							$shipping_method->update_meta_data('chazki_tracking_number', $tracking_id);
							$shipping_method->update_meta_data('chazki_code_delivery', $response['trackCode']);
							$shipping_method->save();
							$order->add_order_note(sprintf(__('El pedido fue notificado a Chazki ( %s ).', 'chazki') , $response['trackCode']));
							//Get Label
							$response_label = $sdk->get_label($tracking_id);
							$order->update_meta_data('_wc_chazki_label', $response_label);
							$order->save();
						}
						if (!($response['ordersWithErrors'] === 0) || !($response['success'] === true)) {
							if (isset($response['errorsDetails'])){
								$msg = '';
								
								foreach ($response['errorsDetails'] as $error){
									foreach( $error['errors'] as $descrip) {
										$msg .= $descrip['description'][0] . ', ';
									}
								}
							} else {
								$msg = $response['errors']['message'];
							}
							$order->add_order_note(sprintf(__('No fue posible notificar el pedido a Chazki ( %s ). La razon es: %s ', 'chazki') , $response['trackCode'],$msg));
						}
					}
					else {
						$order->add_order_note(__('No se envía porque ya se había enviado. Si necesita volver a enviarlo, realizarlo manualmente.', 'chazki'));
					}
				}
			}
		}
	}

	/**
	 * Handles the WooCommerce order Action
	 *
	 * @param WC_Order $order
	 * @return void
	 */
	public static function handle_order_action($order) {					
		$shipping_methods = $order->get_shipping_methods();								
		if (empty($shipping_methods)) {
						return;
		}
		$shipping_method = array_shift($shipping_methods);
		/*Verify It's Chazki Shipping Methos*/
		if ($shipping_method->get_method_id() === 'chazki') {
			$sdk = new ChazkiSdk();
			$response = $sdk->process_order($order);
			if (!$response) {
							$order->add_order_note(__('No fue posible notificar el pedido a Chazki. Contacte al administrador.', 'chazki'));
							return;
			}

			if ($response['ordersWithErrors'] === 0 && $response['success'] === true ) {
				$tracking_id = $response['trackCode'];
				$shipping_method->update_meta_data('chazki_tracking_number', $tracking_id);
				$shipping_method->update_meta_data('chazki_code_delivery', $response['trackCode']);
				$shipping_method->save();
				$order->add_order_note(sprintf(__('El pedido fue notificado a Chazki ( %s ).', 'chazki') , $response['trackCode']));
				//Get Label
				$response_label = $sdk->get_label($tracking_id);
				$order->update_meta_data('_wc_chazki_label', $response_label);
				$order->save();
			}

			if (!($response['ordersWithErrors'] === 0) || !($response['success'] === true)) {
				$order->add_order_note(sprintf(__('No fue posible notificar el pedido a Chazki ( %s ).', 'chazki') , $response['trackCode']));
			}

		}
		else {
			$order->add_order_note(__('No se realiza el envío a Chazki, puesto que no es el método de envío seleccionado.', 'chazki'));
		}
	}

	/**
	 * Adds New Order Action -> Process Chazki Order
	 *
	 * @param arrray $actions      
	 * @return Array
	 */
	public static function add_order_action($actions) {
		global $theorder;
		$shipping_methods = $theorder->get_shipping_methods();
		if (empty($shipping_methods)) {
						return $actions;
		}
		$shipping_method = array_shift($shipping_methods);
		/*Verify It's Chazki Shipping Methos*/
		if ($shipping_method->get_method_id() === 'chazki') {
						$actions['wc_chazki_order_action'] = __('Envío Chazki', 'chazki');
		}
		return $actions;
	}

}
