<?php
namespace Ecomerciar\Chazki\Helper;

trait LoggerTrait {
				private static $logger;

				/**
				 * Inits our logger singleton
				 *
				 * @return void
				 */
				public static function init() {
								if (function_exists('wc_get_logger')) {
												if (!isset(self::$logger)) {
																self::$logger = wc_get_logger();
												}
								}
				}

				/**
				 * Logs an info message
				 *
				 * @param mixed $msg
				 * @return void
				 */
				public static function log_info($msg) {
								self::$logger->info(wc_print_r($msg, true) , ['source' => 'WooCommerce Chazki']);
				}

				/**
				 * Logs an error message
				 *
				 * @param mixed $msg
				 * @return void
				 */
				public static function log_error($msg) {
								self::$logger->error(wc_print_r($msg, true) , ['source' => 'WooCommerce Chazki']);
				}

				/**
				 * Logs an warning message
				 *
				 * @param mixed $msg
				 * @return void
				 */
				public static function log_warning($msg) {
								self::$logger->warning(wc_print_r($msg, true) , ['source' => 'WooCommerce Chazki']);
				}

				/**
				 * Logs a debug message
				 *
				 * @param mixed $msg
				 * @return void
				 */
				public static function log_debug($msg) {
								self::$logger->debug(wc_print_r($msg, true) , ['source' => 'WooCommerce Chazki']);
				}
}
