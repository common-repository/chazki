<?php
namespace Ecomerciar\Chazki\Helper;

class Helper {
				use NoticesTrait;
				use LoggerTrait;
				use SettingsTrait;
				use WooCommerceTrait;
				use ShippingMethodTrait;
				use DatabaseTrait;
				use DebugTrait;
				use ArrayDataTrait;
				/**
				 * Returns an url pointing to the main filder of the plugin assets
				 *
				 * @return string
				 */
				public static function get_assets_folder_url() {
								return plugin_dir_url(\WCChazki::MAIN_FILE) . 'assets';
				}
}
