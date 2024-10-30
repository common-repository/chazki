<?php
/**
 * Plugin Name: Chazki
 * Description: Plugin to connect Chazki's Shipping services with WooCommerce
 * Version: 1.0.0
 * Requires PHP: 7.4
 * Author: Chazki
 * Author URI: https://chazki.com
 * Text Domain: chazki
 * License: GPLv2 or later
 * WC requires at least: 6.0
 * WC tested up to: 6.0
 */

use Ecomerciar\Chazki\Helper\Helper;

defined('ABSPATH') || exit;

add_action('plugins_loaded', ['WCChazki', 'init']);
register_activation_hook(__FILE__, ['WCChazki', 'create_tracking_page']);
register_deactivation_hook(__FILE__, ['WCChazki', 'remove_tracking_page']);
/**
 * Plugin's base Class
 */
class WCChazki {

				const VERSION = '1.0.0';
				const PLUGIN_NAME = 'Chazki';
				const MAIN_FILE = __FILE__;
				const MAIN_DIR = __DIR__;

				/**
				 * Checks system requirements
				 *
				 * @return bool
				 */
				public static function check_system() {
								require_once ABSPATH . 'wp-admin/includes/plugin.php';
								$system = self::check_components();

								if ($system['flag']) {
												deactivate_plugins(plugin_basename(__FILE__));
												echo '<div class="notice notice-error is-dismissible">';
												echo '<p>' . sprintf(__('<strong>%s/strong> Requiere al menos %s versión %s o superior.', 'chazki') , self::PLUGIN_NAME, esc_html($system['flag']), esc_html($system['version'])) . '</p>';
												echo '</div>';
												return false;
								}

								if (!class_exists('WooCommerce')) {
												deactivate_plugins(plugin_basename(__FILE__));
												echo '<div class="notice notice-error is-dismissible">';
												echo '<p>' . sprintf(__('WooCommerce debe estar activo antes de usar <strong>%s</strong>', 'chazki') , self::PLUGIN_NAME) . '</p>';
												echo '</div>';
												return false;
								}
								return true;
				}

				/**
				 * Check the components required for the plugin to work (PHP, WordPress and WooCommerce)
				 *
				 * @return array
				 */
				private static function check_components() {
								global $wp_version;
								$flag = $version = false;

								if (version_compare(PHP_VERSION, '7.4', '<')) {
												$flag = 'PHP';
												$version = '7.4';
								}
								elseif (version_compare($wp_version, '6.0', '<')) {
												$flag = 'WordPress';
												$version = '6.0';
								}
								elseif (!defined('WC_VERSION') || version_compare(WC_VERSION, '6.7', '<')) {
												$flag = 'WooCommerce';
												$version = '6.7';
								}

								return ['flag' => $flag, 'version' => $version];
				}

				/**
				 * Inits our plugin
				 *
				 * @return void
				 */
				public static function init() {
								if (!self::check_system()) {
												return false;
								}
								spl_autoload_register(function ($class) {
												if (strpos($class, 'Chazki') === false) {
																return;
												}

												$name = str_replace('\\', '/', $class);
												$name = str_replace('Ecomerciar/Chazki/', '', $name);

												require_once plugin_dir_path(__FILE__) . $name . '.php';
								});
								include_once __DIR__ . '/Hooks.php';
								Helper::init();
								self::load_textdomain();
				}

				/**
				 * Create a link to the settings page, in the plugins page
				 *
				 * @param array $links
				 * @return array
				 */
				public static function create_settings_link(array $links) {
								$link = '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=wc-settings&tab=shipping&section=chazki_shipping_options')) . '">' . __('Ajustes', 'chazki') . '</a>';
								array_unshift($links, $link);
								return $links;
				}

				/**
				 * Adds our shipping method to WooCommerce
				 *
				 * @param array $shipping_methods
				 * @return array
				 */
				public static function add_shipping_method($shipping_methods) {
								$shipping_methods['chazki'] = '\Ecomerciar\Chazki\ShippingMethod\WC_Chazki';
								return $shipping_methods;
				}

				/**
				 * Loads the plugin text domain
				 *
				 * @return void
				 */
				public static function load_textdomain() {
								load_plugin_textdomain('chazki', false, basename(dirname(__FILE__)) . '/i18n/languages');
				}

        /**
				 * Creates Chazki Tracking Page
				 *
				 * @return void
				 */
				public static function create_tracking_page() {
				        $tracking_page_id = get_option('chazki-tracking-form-page', false);

								$post_details = array(
												'post_title' => __('Tracking Page', 'chazki') ,
												'post_content' => '[chazki-tracking-form]',
												'post_status' => 'publish',
												'post_type' => 'page'
								);
								$post_id = wp_insert_post($post_details);

								update_option('chazki-tracking-form-page', $post_id);
                return;
				}

				public static function remove_tracking_page(){
					$tracking_page_id = get_option('chazki-tracking-form-page', false);
    				wp_delete_post($tracking_page_id);
				}

}
?>
