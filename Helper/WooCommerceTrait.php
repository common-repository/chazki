<?php
namespace Ecomerciar\Chazki\Helper;

trait WooCommerceTrait {

				/**
				 * Gets the customer from a WooCommerce Cart
				 *
				 * @param WC_Customer $customer
				 * @return array|false
				 */
				public static function get_customer_from_cart($customer) {
								if (!$customer) return false;
								$name = self::get_customer_name($customer);
								$first_name = self::get_customer_first_name($customer);
								$last_name = self::get_customer_last_name($customer);
								$address = self::get_addressAsEntered($customer);
								$postal_code = self::get_postal_code($customer);
								$province = self::get_province($customer);
								$locality = self::get_locality($customer);
								$district = self::get_district($customer);
								// $full_address = self::get_full_address($address, $locality, $postal_code, $province);
								// return ['first_name' => $first_name, 'last_name' => $last_name, 'full_name' => $name, 'street' => $address['street'], 'number' => $address['number'], 'floor' => $address['floor'], 'apartment' => $address['apartment'], 'full_address' => $full_address, 'cp' => $postal_code, 'locality' => $locality, 'province' => $province, 'district' => $district];
								return ['first_name' => $first_name, 'last_name' => $last_name, 'full_name' => $name,  'full_address' => $address, 'cp' => $postal_code, 'locality' => $locality, 'province' => $province, 'district' => $district];
				}

				/**
				 * Gets full address
				 *
				 * @param array $address
				 * @param string $locality
				 * @param string $postal_code
				 * @param string $province
				 * @return string
				 */
				public static function get_full_address(array $address, string $locality, string $postal_code, string $province) {
								$full_address = $address['street'];
								if (!empty($address['number'])) {
												$full_address .= ' ' . $address['number'];
								}
								if (!empty($address['floor'])) {
												$full_address .= ', ';
												$full_address .= $address['floor'];
												if (!empty($address['apartment'])) {
																$full_address .= ' ' . $address['apartment'];
												}
								}
								$full_address .= '. ';
								$full_address .= $locality . ' ' . $postal_code . ', ' . $province;
								return $full_address;
				}

				public static function get_only_address(array $address) {
					$full_address = $address['street'];
					if (!empty($address['number'])) {
									$full_address .= ' ' . $address['number'];
					}
					if (!empty($address['floor'])) {
									$full_address .= ', ';
									$full_address .= $address['floor'];
									if (!empty($address['apartment'])) {
													$full_address .= ' ' . $address['apartment'];
									}
					}					
					return $full_address;
	}

				/**
				 * Gets customer data from an order
				 *
				 * @param WC_Order $order
				 * @return array|false
				 */
				public static function get_customer_from_order($order) {
								if (!$order) return false;
								$data = self::get_customer_from_cart($order);
								$data['email'] = $order->get_billing_email();
								$data['phone'] = $order->get_billing_phone();
								$data['extra_info'] = $order->get_customer_note();
								return $data;
				}

				/**
				 * Gets the District from acustomer
				 *
				 * @param WC_Customer $customer
				 * @return string
				 */
				public static function get_district($customer) {
					$district = '';
					if (get_post_meta($customer->get_id() , '_shipping_wc_chazki_district', true)) {
						$district = get_post_meta($customer->get_id() , '_shipping_wc_chazki_district', true);
					}
					else {
						$district = get_post_meta($customer->get_id() , '_billing_wc_chazki_district', true);
					}
					return $district;
				}

				/**
				 * Gets the province from a customer
				 *
				 * @param WC_Customer $customer
				 * @return string
				 */
				public static function get_province($customer) {
								$province = '';
								if (!($province = $customer->get_shipping_state())) {
												$province = $customer->get_billing_state();
								}
								return self::get_province_name($province);
				}

				/**
				 * Gets the locality from a customer
				 *
				 * @param WC_Customer $customer
				 * @return string
				 */
				public static function get_locality($customer) {
								$locality = '';
								if (!($locality = $customer->get_shipping_city())) {
												$locality = $customer->get_billing_city();
								}
								return $locality;
				}

				/**
				 * Gets the postal code from a customer
				 *
				 * @param WC_Customer $customer
				 * @return string
				 */
				public static function get_postal_code($customer) {
								$postal_code = '';
								if (!($postal_code = $customer->get_shipping_postcode())) {
												$postal_code = $customer->get_billing_postcode();
								}
								return $postal_code;
				}

				/**
				 * Gets the full customer name
				 *
				 * @param WC_Customer $customer
				 * @return string
				 */
				public static function get_customer_name($customer) {
								$name = '';
								$name = self::get_customer_first_name($customer) . ' ' . self::get_customer_last_name($customer);
								return $name;
				}

				/**
				 * Gets the customer first name
				 *
				 * @param WC_Customer $customer
				 * @return string
				 */
				public static function get_customer_first_name($customer) {
								$name = '';
								if ($customer->get_shipping_first_name()) {
												$name = $customer->get_shipping_first_name();
								}
								else {
												$name = $customer->get_billing_first_name();
								}
								return $name;
				}

				/**
				 * Gets the customer last name
				 *
				 * @param WC_Customer $customer
				 * @return string
				 */
				public static function get_customer_last_name($customer) {
								$name = false;
								if ($customer->get_shipping_last_name()) {
												$name = $customer->get_shipping_last_name();
								}
								else {
												$name = $customer->get_billing_last_name();
								}
								return $name;
				}

				public static function get_addressAsEntered($order) {
					if (!$order) return false;
					
					if ($order->get_shipping_address_1()) {
									$shipping_line_1 = $order->get_shipping_address_1();
									$shipping_line_2 = $order->get_shipping_address_2();
					}
					else {
									$shipping_line_1 = $order->get_billing_address_1();
									$shipping_line_2 = $order->get_billing_address_2();
					}
					return empty($shipping_line_1)? $shipping_line_2 : $shipping_line_1;
				}

				/**
				 * Gets the address of an order
				 *
				 * @param WC_Order $order
				 * @return false|array
				 */
				public static function get_address($order) {
								if (!$order) return false;
								if ($order->get_shipping_address_1()) {
												$shipping_line_1 = $order->get_shipping_address_1();
												$shipping_line_2 = $order->get_shipping_address_2();
								}
								else {
												$shipping_line_1 = $order->get_billing_address_1();
												$shipping_line_2 = $order->get_billing_address_2();
								}
								$street_name = $street_number = $floor = $apartment = "";
								if (!empty($shipping_line_2)) {
												//there is something in the second line. Let's find out what
												$fl_apt_array = self::get_floor_and_apt($shipping_line_2);
												$floor = $fl_apt_array[0];
												$apartment = $fl_apt_array[1];
								}

								//Now let's work on the first line
								preg_match('/(^\d*[\D]*)(\d+)(.*)/i', $shipping_line_1, $res);
								$line1 = $res;

								if ((isset($line1[1]) && !empty($line1[1]) && $line1[1] !== " ") && !empty($line1)) {
												//everything's fine. Go ahead
												if (empty($line1[3]) || $line1[3] === " ") {
																//the user just wrote the street name and number, as he should
																$street_name = trim($line1[1]);
																$street_number = trim($line1[2]);
																unset($line1[3]);
												}
												else {
																//there is something extra in the first line. We'll save it in case it's important
																$street_name = trim($line1[1]);
																$street_number = trim($line1[2]);
																$shipping_line_2 = trim($line1[3]);

																if (empty($floor) && empty($apartment)) {
																				//if we don't have either the floor or the apartment, they should be in our new $shipping_line_2
																				$fl_apt_array = self::get_floor_and_apt($shipping_line_2);
																				$floor = $fl_apt_array[0];
																				$apartment = $fl_apt_array[1];
																}
																elseif (empty($apartment)) {
																				//we've already have the floor. We just need the apartment
																				$apartment = trim($line1[3]);
																}
																else {
																				//we've got the apartment, so let's just save the floor
																				$floor = trim($line1[3]);
																}
												}
								}
								else {
												//the user didn't write the street number. Maybe it's in the second line
												//given the fact that there is no street number in the fist line, we'll asume it's just the street name
												$street_name = $shipping_line_1;

												if (!empty($floor) && !empty($apartment)) {
																//we are in a pickle. It's a risky move, but we'll move everything one step up
																$street_number = $floor;
																$floor = $apartment;
																$apartment = "";
												}
												elseif (!empty($floor) && empty($apartment)) {
																//it seems the user wrote only the street number in the second line. Let's move it up
																$street_number = $floor;
																$floor = "";
												}
												elseif (empty($floor) && !empty($apartment)) {
																//I don't think there's a chance of this even happening, but let's write it to be safe
																$street_number = $apartment;
																$apartment = "";
												}
								}
								return array(
												'street' => $street_name,
												'number' => $street_number,
												'floor' => $floor,
												'apartment' => $apartment
								);
				}

				/**
				 * Get specific details from an address (floor and apt)
				 *
				 * @param string $fl_apt
				 * @return array
				 */
				public static function get_floor_and_apt($fl_apt) {
								//firts we'll asume the user did things right. Something like "piso 24, depto. 5h"
								preg_match('/(piso|p|p.) ?(\w+),? ?(departamento|depto|dept|dpto|dpt|dpt.º|depto.|dept.|dpto.|dpt.|apartamento|apto|apt|apto.|apt.) ?(\w+)/i', $fl_apt, $res);
								$line2 = $res;

								if (!empty($line2)) {
												//everything was written great. Now lets grab what matters
												$floor = trim($line2[2]);
												$apartment = trim($line2[4]);
								}
								else {
												//maybe the user wrote something like "depto. 5, piso 24". Let's try that
												preg_match('/(departamento|depto|dept|dpto|dpt|dpt.º|depto.|dept.|dpto.|dpt.|apartamento|apto|apt|apto.|apt.) ?(\w+),? ?(piso|p|p.) ?(\w+)/i', $fl_apt, $res);
												$line2 = $res;
								}

								if (!empty($line2) && empty($apartment) && empty($floor)) {
												//apparently, that was the case. Guess some people just like to make things difficult
												$floor = trim($line2[4]);
												$apartment = trim($line2[2]);
								}
								else {
												//something is wrong. Let's be more specific. First we'll try with only the floor
												preg_match('/^(piso|p|p.) ?(\w+)$/i', $fl_apt, $res);
												$line2 = $res;
								}

								if (!empty($line2) && empty($floor)) {
												//now we've got it! The user just wrote the floor number. Now lets grab what matters
												$floor = trim($line2[2]);
								}
								else {
												//still no. Now we'll try with the apartment
												preg_match('/^(departamento|depto|dept|dpto|dpt|dpt.º|depto.|dept.|dpto.|dpt.|apartamento|apto|apt|apto.|apt.) ?(\w+)$/i', $fl_apt, $res);
												$line2 = $res;
								}

								if (!empty($line2) && empty($apartment) && empty($floor)) {
												//success! The user just wrote the apartment information. No clue why, but who am I to judge
												$apartment = trim($line2[2]);
								}
								else {
												//ok, weird. Now we'll try a more generic approach just in case the user missplelled something
												preg_match('/(\d+),? [a-zA-Z.,!*]* ?([a-zA-Z0-9 ]+)/i', $fl_apt, $res);
												$line2 = $res;
								}

								if (!empty($line2) && empty($floor) && empty($apartment)) {
												//finally! The user just missplelled something. It happens to the best of us
												$floor = trim($line2[1]);
												$apartment = trim($line2[2]);
								}
								else {
												//last try! This one is in case the user wrote the floor and apartment together ("12C")
												preg_match('/(\d+)(\D*)/i', $fl_apt, $res);
												$line2 = $res;
								}

								if (!empty($line2) && empty($floor) && empty($apartment)) {
												//ok, we've got it. I was starting to panic
												$floor = trim($line2[1]);
												$apartment = trim($line2[2]);
								}
								elseif (empty($floor) && empty($apartment)) {
												//I give up. I can't make sense of it. We'll save it in case it's something useful
												$floor = $fl_apt;
								}

								return array(
												$floor,
												$apartment
								);
				}

				/**
				 * Gets the province name
				 *
				 * @param string $province_id
				 * @return string
				 */
				public static function get_province_name(string $province_id = '') {
								switch ($province_id) {
												/*Mexico*/
												case 'DF':
												$zone = 'Ciudad de Mexico';
												break;
												case 'JA':
												$zone = 'Jalisco';
												break;
												case 'NL':
												$zone = 'Nuevo León';
												break;
												case 'AG':
												$zone ='Aguascalientes';
												break;
												case 'BC':
												$zone = 'Baja California';
												break;
												case 'BS':
												$zone ='Baja California Sur';
												break;
												case 'CM':
												$zone = 'Campeche';
												break;
												case 'CS':
												$zone = 'Chiapas';
												break;
												case 'CH':
												$zone = 'Chihuahua';
												break;
												case 'CO':
												$zone = 'Coahuila';
												break;
												case 'CL':
												$zone = 'Colima';
												break;
												case 'DG':
												$zone = 'Durango';
												break;
												case 'GT':
												$zone = 'Guanajuato';
												break;
												case 'GR':
												$zone = 'Guerrero';
												break;
												case 'HG':
												$zone = 'Hidalgo';
												break;
												case 'MX':
												$zone = 'Estado de México';
												break;
												case 'MI':
												$zone = 'Michoacán';
												break;
												case 'MO':
												$zone = 'Morelos';
												break;
												case 'NA':
												$zone = 'Nayarit';
												break;
												case 'OA':
												$zone = 'Oaxaca';
												break;
												case 'PU':
												$zone = 'Puebla';
												break;
												case 'QR':
												$zone = 'Quintana Roo';
												break;
												case 'QT':
												$zone = 'Querétaro';
												break;
												case 'SL':
												$zone = 'San Luis Potosí';
												break;
												case 'SI':
												$zone = 'Sinaloa';
												break;
												case 'SO':
												$zone = 'Sonora';
												break;
												case 'TB':
												$zone = 'Tabasco';
												break;
												case 'TM':
												$zone = 'Tamaulipas';
												break;
												case 'TL':
												$zone = 'Tlaxcala';
												break;
												case 'VE':
												$zone = 'Veracruz';
												break;
												case 'YU':
												$zone = 'Yucatán';
												break;
												case 'ZA':
												$zone = 'Zacatecas';
												break;

												/*Peru*/
												case 'CAL':
													$zone = 'El Callao';
													break;
													case 'LMA':
													$zone = 'Municipalidad Metropolitana de Lima';
													break;
													case 'AMA':
													$zone = 'Amazonas';
													break;
													case 'ANC':
													$zone = 'Ancash';
													break;
													case 'APU':
													$zone = 'Apurímac';
													break;
													case 'ARE':
													$zone = 'Arequipa';
													break;
													case 'AYA':
													$zone = 'Ayacucho';
													break;
													case 'CAJ':
													$zone = 'Cajamarca';
													break;
													case 'CUS':
													$zone = 'Cusco';
													break;
													case 'HUV':
													$zone = 'Huancavelica';
													break;
													case 'HUC':
													$zone = 'Huánuco';
													break;
													case 'ICA':
													$zone = 'Ica';
													break;
													case 'JUN':
													$zone = 'Junín';
													break;
													case 'LAL':
													$zone = 'La Libertad';
													break;
													case 'LA':
													$zone = '>Lambayeque';
													break;
													case 'LIM':
													$zone = 'Lima';
													break;
													case 'LOR':
													$zone = 'Loreto';
													break;
													case 'MDD':
													$zone = 'Madre de Dios';
													break;
													case 'MOQ':
													$zone = 'Moquegua';
													break;
													case 'PAS':
													$zone = 'Pasco';
													break;
													case 'PIU':
													$zone = 'Piura';
													break;
													case 'PUN':
													$zone = 'Puno';
													break;
													case 'SAM':
													$zone = 'San Martín';
													break;
													case 'TAC':
													$zone = 'Tacna';
													break;
													case 'TUM':
													$zone = 'Tumbes';
													break;
													case 'UCA':
													$zone = 'Ucayali';
													break;
												default:
																$zone = $province_id;
																break;
								}
								return $zone;
				}

				/**
				 * Gets product dimensions and details
				 *
				 * @param int $product_id
				 * @return false|array
				 */
				public static function get_product_dimensions($product_id) {
								$product = wc_get_product($product_id);
								if (!$product) return false;
								if (empty($product->get_height()) || empty($product->get_length()) || empty($product->get_width()) || !$product->has_weight()) {
												return false;
								}
								$dimension_unit = 'cm';
								$weight_unit = 'g';

								$height = ($product->get_height() ? wc_get_dimension($product->get_height() , $dimension_unit) : '0');
								$width = ($product->get_width() ? wc_get_dimension($product->get_width() , $dimension_unit) : '0');
								$length = ($product->get_length() ? wc_get_dimension($product->get_length() , $dimension_unit) : '0');

								$new_product = array(
												'height' => ($product->get_height() ? wc_get_dimension($product->get_height() , $dimension_unit) : '0') ,
												'width' => ($product->get_width() ? wc_get_dimension($product->get_width() , $dimension_unit) : '0') ,
												'length' => ($product->get_length() ? wc_get_dimension($product->get_length() , $dimension_unit) : '0') ,
												'weight' => ($product->has_weight() ? wc_get_weight($product->get_weight() , $weight_unit) : '0') ,
												'price' => $product->get_price() ,
												'description' => $product->get_name() ,
												'id' => $product_id,
												/*'chazki-product-size' =>  get_post_meta( $product_id , 'wc_chazki_product_size' , true )*/
												'chazki-product-size' => self::get_product_size($height, $width, $length)
								);
								return $new_product;
				}

        /**
         * Gets all items from a cart
         *
         * @param int $height
         * @param int $width
         * @param int $length
         * @return string
         */
				public static function get_product_size($height, $width, $length) {
								/*
								XS: 00X00X00 - 10X10X20
								 S: 10X10X20 - 20X20X30
								 M: 20X20X30 - 40X40X30
								 L: 40X40X30 - 50X50X50
								XL: 50X50X50 - 90X70X60
								*/

								$dims = array(
												$height,
												$width,
												$length
								);
								asort($dims);

								$product_size_dims = array(
												array(
																'id' => 'XS',
																'x' => 20,
																'y' => 10,
																'z' => 10
												) ,
												array(
																'id' => 'S',
																'x' => 30,
																'y' => 20,
																'z' => 20
												) ,
												array(
																'id' => 'M',
																'x' => 40,
																'y' => 40,
																'z' => 30
												) ,
												array(
																'id' => 'L',
																'x' => 50,
																'y' => 50,
																'z' => 50
												) ,
												array(
																'id' => 'XL',
																'x' => 90,
																'y' => 70,
																'z' => 60
												)
								);

								foreach ($product_size_dims as $product_size_dim) {
												if ($dims[0] <= $product_size_dim['x'] && $dims[1] <= $product_size_dim['y'] && $dims[2] <= $product_size_dim['z']) {
																return $product_size_dim['id'];
												}
								}
								self::log_error(__('Helper -> Error obteniendo el tamaño del paquete, producto con malas dimensiones - ID: ', 'chazki') . $product_id);
								return "";
				}

				/**
				 * Gets all items from a cart
				 *
				 * @param WC_Cart $cart
				 * @return false|array
				 */
				public static function get_items_from_cart($cart) {
								$products = array();
								$items = $cart->get_cart();
								foreach ($items as $item) {
												$product_id = $item['data']->get_id();
												$new_product = self::get_product_dimensions($product_id);
												if (!$new_product) {
																self::log_error(__('Helper -> Error obteniendo productos del carrito, producto con malas dimensiones - ID: ','chazki') . $product_id);
																return false;
												}
												for ($i = 0;$i < $item['quantity'];$i++) $products[] = $new_product;
								}
								return $products;
				}

				/**
				 * Gets items from an order
				 *
				 * @param WC_Order $order
				 * @return false|array
				 */
				public static function get_items_from_order($order) {
								$products = array();
								$items = $order->get_items();
								foreach ($items as $item) {
												$product_id = $item->get_variation_id();
												if (!$product_id) $product_id = $item->get_product_id();
												$new_product = self::get_product_dimensions($product_id);
												if (!$new_product) {
																self::log_error(__('Helper -> Error obteniendo productos de la orden, producto con malas dimensiones - ID: ','chazki') . $product_id);
																return false;
												}
												for ($i = 0;$i < $item->get_quantity();$i++) {
																$products[] = $new_product;
												}
								}
								return $products;
				}

				/**
				 * Groups an array of items
				 *
				 * @param array $items
				 * @return array
				 */
				public static function group_items(array $items) {
								$grouped_items = [];
								foreach ($items as $item) {
												if (isset($grouped_items[$item['id']])) {
																$grouped_items[$item['id']]['quantity']++;
												}
												else {
																$grouped_items[$item['id']] = $item;
																$grouped_items[$item['id']]['quantity'] = 1;
												}
								}
								return $grouped_items;
				}
}
