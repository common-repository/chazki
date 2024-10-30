<?php
namespace Ecomerciar\Chazki\Orders;

use Ecomerciar\Chazki\Helper\Helper;
use Ecomerciar\Chazki\Sdk\ChazkiSdk;

defined('ABSPATH') || exit;

/**
 * WooCommerce Order Metabox's base Class
 */
class Metabox {
	/**
	 * Creates Metabos
	 *
	 * @return void
	 */
	public static function create() {
		$order_types = wc_get_order_types('order-meta-boxes');
		foreach ($order_types as $order_type) {
			add_meta_box('chazki_metabox', // Unique ID
			'Chazki', // Box title
			[__CLASS__, 'content'], // Content callback, must be of type callable
			$order_type, 'side', 'default');
		}
	}

/**
 * Prints Metabox Contents
 *
 * @param WC_Post $post
 * @param Metabox $metabox
 * @return void
 */
	public static function content($post, $metabox) {
		$settings = Helper::get_setup_from_settings();

		$order = wc_get_order($post->ID);
		if (empty($order)) {
						return false;
		}

		$shipping_methods = $order->get_shipping_methods();
		if (empty($shipping_methods)) {
						echo __('El pedido no tiene Chazki como método de envío.', 'chazki');
						return true;
		}
		$shipping_method = array_shift($shipping_methods);
		if ($shipping_method->get_method_id() === 'chazki') {
			if (!empty($shipping_method->get_meta('chazki_tracking_number'))) {
				$tracking_number = $shipping_method->get_meta('chazki_tracking_number');

				if (!empty($settings['tracking-form-page'])) {
					$tracking_page = get_post($settings['tracking-form-page']);
					$url = esc_url(get_page_link($tracking_page));
				}

				if (!empty($url)) {
					echo "<span>" . __("Código Seguimiento:", 'chazki') . "</span>" . "<a href='" .  esc_url($url . "?chazkitrackid=" . $shipping_method->get_meta('chazki_tracking_number')) . "'>" . esc_html( $shipping_method->get_meta('chazki_tracking_number') ). "</a>";
				}
				else if ($tracking_number){
					
					echo "<span>  Código Seguimiento: </span> <a href='". esc_url("https://nintendo". $settings['environment'] .".chazki.com/trackcodeTracking/ "  . $tracking_number) . "'>". esc_html( $tracking_number) . "</a>";
				} else {
					echo "Codigo de Seguimiento: No reconocido ";
				}

				$label_64 = $order->get_meta('_wc_chazki_label');
				if(!empty($label_64)){					
					$label_64 = esc_url($label_64);
					echo "<br/><a class='button button-primary' href='{$label_64}' >" . __('Descargar Etiqueta') . '</a>';
				} 						
			}
			else {
				echo __('El pedido aún no fue informado a Chazki.', 'chazki');
			}

		}
		else {
			echo __('El pedido no tiene Chazki como método de envío.', 'chazki');
		}
	}
}
