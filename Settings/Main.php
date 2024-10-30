<?php
namespace Ecomerciar\Chazki\Settings;

use Ecomerciar\Chazki\Settings\Section;
use Ecomerciar\Chazki\Helper\Helper;

defined('ABSPATH') || exit;

/**
 * A main class that holds all our settings logic
 */
class Main {
      /**
       * Add Chazki Setting Tab
       *
       * @param Array $settings_tab Shipping Methods
       * @return Array Shipping Methods
       */
				public static function add_tab_settings($settings_tab) {
								$settings_tab['chazki_shipping_options'] = __('Chazki');
								return $settings_tab;
				}

        /**
         * Get Chazki Setting Tab
         *
         * @param Array $settings Shipping Methods
         * @param string $current_section Section which is beaing processing
         * @return Array Shipping Method Settings
         */
				public static function get_tab_settings($settings, $current_section) {
								if ('chazki_shipping_options' == $current_section) {
												return Section::get();
								}
								else {
												return $settings;
								}
				}

        /**
         * Get Chazki Settings
         *
         * @return Array Shipping Methods
         */
				public static function get_settings() {
								return apply_filters('wc_settings_chazki_shipping_options', Section::get());
				}

        /**
         * Update Chazki Settings
         *
         * @return Void
         */
				public static function update_settings() {                
								woocommerce_update_options(self::get_settings());
				}              
}
