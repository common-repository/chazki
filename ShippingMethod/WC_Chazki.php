<?php
namespace Ecomerciar\Chazki\ShippingMethod;

use WC_Shipping_Method;
use Ecomerciar\Chazki\Helper\Helper;
defined('ABSPATH') || class_exists('\WC_Shipping_Method') || exit;

/**
 * Our main payment method class
 */
class WC_Chazki extends \WC_Shipping_Method {
				 /**
 				 * Default constructor
 				 *
         * @param int $instance_id Shipping Method Instance from Order
 				 * @return void
 				 */
				public function __construct($instance_id = 0) {
								$this->id = 'chazki';
								$this->instance_id = absint($instance_id);
								$this->method_title = __('Chazki', 'chazki');
								$this->method_description = __('Permite a tus clientes recibir sus pedidos con Chazki.', 'chazki');
								$this->supports = array(
												'shipping-zones',
												'instance-settings',
												'instance-settings-modal',
								);
								$this->init();

								add_action('woocommerce_update_options_shipping_' . $this->id, array(
												$this,
												'process_admin_options'
								));
				}

        /**
        * Init user set variables.
        *
        * @return void
        */
				public function init() {
								$this->instance_form_fields = include 'settings-chazki.php';
								$this->title = $this->get_option('title');
								$this->cost = $this->get_option('cost');
								$this->type = $this->get_option('type', 'class');
								$this->service = $this->get_option('service');
								$this->categories = is_array($this->get_option('category'))? $this->get_option('category') : array($this->get_option('category')) ;								
								$this->dimensions = is_array($this->get_option('dimension'))? $this->get_option('dimension') : array($this->get_option('dimension')) ;
								// Save settings in admin if you have any defined
								add_action('woocommerce_update_options_shipping_' . $this->id, array(
												$this,
												'process_admin_options'
								));
				}

				/**
				 * Sanitize the cost field.
         *
				 * @return string
				 */
				public function sanitize_cost($value) {
								$value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
								return $value;
				}

				/**
				 * Calculate the shipping costs.
				 *
				 * @param array $package Package of items from cart.
         * @return void
				 */
				public function calculate_shipping($package = array()) {
								$rate = array(
												'label' => $this->get_option('title') , // Label for the rate
												'cost' => '0', // Amount for shipping or an array of costs (for per item shipping)
												'taxes' => '', // Pass an array of taxes, or pass nothing to have it calculated for you, or pass 'false' to calculate no tax for this method
												'calc_tax' => 'per_order', // Calc tax per_order or per_item. Per item needs an array of costs passed via 'cost'
												'package' => $package
								);

								$has_costs = false;
								$cost = $this->get_option('cost');

								if ('' !== $cost) {
												$has_costs = true;
												$rate['cost'] = $this->sanitize_cost($cost);
								}

								if ($has_costs && $this->all_products_categories_defined() && $this->all_producto_sizes_defined()) {
												// Register the rate
												$this->add_rate($rate);
								}

				}

        /**
				 * Get Chazki Service Value
				 *
         * @return string
				 */
				public function get_service() {
								return $this->service;
				}

	/**
				 * Get Chazki Cost Service Value
				 *
         * @return string
				 */
				public function get_cost() {
					return $this->cost;
	}

	private function all_products_categories_defined(){						
		Helper::log( 'categories ==============================================>' . json_encode($this->categories ));
		if (in_array('-1', $this->categories)) {
			return true;
		}
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {			
			$term_obj_list = get_the_terms( $cart_item[ 'product_id' ], 'product_cat' );
			$terms_string = wp_list_pluck($term_obj_list, 'term_id') ;
			foreach ( $terms_string as $term ){
				if ( !in_array($term, $this->categories) ) {
					return false;
				}
			}
		}
		return true;
	}

	private function all_producto_sizes_defined(){
		Helper::log( 'sizes ==============================================>' . json_encode($this->dimensions ));
		if (in_array('None', $this->dimensions)) {
			return true;
		}
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {						
			$prod = Helper::get_product_dimensions($cart_item[ 'product_id' ]) ;					
			if ( $prod && $prod['chazki-product-size'] && !in_array($prod['chazki-product-size'], $this->dimensions) ) {
				return false;
			}
		
		}
		return true;
	}

}
