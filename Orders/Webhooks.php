<?php
namespace Ecomerciar\Chazki\Orders;

use Ecomerciar\Chazki\Helper\Helper;

defined('ABSPATH') || exit;

/**
 * WebHook's base Class
 */
class Webhooks {

				/**
				 * Receives the webhook and check if it's valid to proceed
				 *
				 * @return void
				 */
				public static function listener() {
								// Takes raw data from the request
								$json = file_get_contents('php://input');

								// Converts it into a PHP object
								$data = json_decode($json, true);

								Helper::log_info('Webhook recibido');
								if (Helper::get_option('debug')) {
												Helper::log_debug(__FUNCTION__ . __('- Webhook recibido de Chazki:', 'chazki') . json_encode($json));
								}
								if (empty($json) || !self::validate_input($data)) {
												wp_die(__('WooCommerce Chazki Webhook no válido.', 'chazki'), 'Chazki Webhook', ['response' => 500]);
								}
				}

				/**
				 * Validates the incoming webhook
				 *
				 * @param array $data
				 * @return bool
				 */
				private static function validate_input(array $data) {
								$data = wp_unslash($data);
								if (empty($data['delivery_code'])) {
												return false;
								}
								if (empty($data['delivery_status'])) {
												return false;
								}
								$chazki_id = filter_var($data['delivery_code'], FILTER_SANITIZE_STRING);
								$order_id = Helper::find_order_by_itemmeta_value($chazki_id);
								if (empty($order_id)) {
												return false;
								}
								self::handle_webhook($order_id, $data);
								return true;
				}

				/**
				 * Handles and processes the webhook
				 *
				 * @param int $order_id
				 * @param array $data
				 * @return void
				 */
				private static function handle_webhook(int $order_id, array $data) {
								$order = wc_get_order($order_id);
								$status = self::translate_order_status($data['delivery_status']);
								$order->add_order_note('Chazki - ' . $status . '.  - ' . $data['timestamp'] . __(' (UTC -0)', 'chazli'));
								$order->save();
								Helper::log_info(sprintf(__('La Orden #%s fue actualizada con el estado: %s', 'chazki'), $order_id, $status));
								return true;
				}

				/**
				 * Translates an order status (from Moova)
				 *
				 * @param string $status
				 * @return string
				 */
				private static function translate_order_status(string $status) {
								$translations = [
                                    'NEW'         => __('El pedido es recién ingresado al sistema', 'chazki') ,
                                    'PRE_OFFERED' => __('Estado previo a que el pedido pase a planeamiento y sea ofertado', 'chazki') ,
                                    'OFFERED'     => __('El pedido es ofrecido para que un Chazki lo acepte', 'chazki') ,
                                    'WAITING'     => __('El Chazki va camino a recoger el pedido', 'chazki') ,
                                    'ARRIVED'     => __('El Chazki llega al punto de recojo del pedido', 'chazki') ,
                                    'FAILED_PICK' => __('Falla durante el recojo del pedido', 'chazki') ,
                                    'IN_PROGRESS' => __('El Chazki está camino a entregar el pedido', 'chazki') ,
                                    'FAILED'      => __('Falla ocurrida durante la entrega del pedido', 'chazki') ,
                                    'COMPLETED'   => __('El pedido es entregado con éxito', 'chazki')
                                ];
								return (isset($translations[$status]) ? $translations[$status] : 'El envío está en estado ' . $status);
				}

}
