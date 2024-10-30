<?php
namespace Ecomerciar\Chazki\Helper;

trait SettingsTrait {

				/**
				 * Gets a plugin option
				 *
				 * @param string $key
				 * @param boolean $default
				 * @return mixed
				 */
				public static function get_option(string $key, $default = false) {
								return get_option('chazki-' . $key, $default);
				}

				/**
				 * Gets the seller settings
				 *
				 * @return array
				 */
				public static function get_setup_from_settings() {
								return ['branch-id' => self::get_option('branch-id') , 
										'api-key' => self::get_option('api-key') , 
										'proof-payment' => self::get_option('proof-payment') , 
										'country' => self::get_option('country') ,
										'payment-method' => self::get_option( 'payment-method' ), 
										'process-order-status' => str_replace('wc-', '', self::get_option('process-order-status')) , 
										'environment' => '' , /* Options: -dev | -beta | '' */
										'tracking-form-page' => self::get_option('tracking-form-page'),
										'debug' => self::get_option('debug') ];
				}

}
