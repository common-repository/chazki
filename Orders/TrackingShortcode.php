<?php
namespace Ecomerciar\Chazki\Orders;

use Ecomerciar\Chazki\Helper\Helper;
use Ecomerciar\Chazki\Sdk\ChazkiSdk;

defined('ABSPATH') || exit;

/**
 * Tracking Shortode's base Class
 */
class TrackingShortcode {

				/**
				 * Generates Shortcode Output
				 *
				 * @return string
				 */
				public static function output() {
								if (isset($_GET["chazkitrackid"]) ){
									$trackid = sanitize_text_field($_GET['chazkitrackid']);
								} else {
									$trackid = '';
								}
								$content = '';
								$allowedTags = array(
									'a' => array(
										'href' 	=> array(), 
										'title' => array()
									), 
									'form' 	=> array(
										'method' => array(),
										'class' => array(),
										'style' => array(),
									) ,
									'input' => array(
										'name' => array(),
										'type' => array(),
										'value' => array(),
										'id' => array(),
										'class' => array(),
									),
									'style' => array(),
									'div'	=> array(												
												'style' => array(),
												'class' => array()
												),
									'img'	=> array(
										'style' => array(),
										'src' => array()
									),
									'h2' => array(
										'class' => array()
									),
									'br' => array()
								);
								$logo_url = Helper::get_assets_folder_url() . '/img/logo.png';
								
								$content .= '<div class="card-body" style="text-align:center;"> <img src="' . $logo_url . '" style="display: block; margin-left:auto; margin-right:auto;">';
								$content .= '<h2 class="chazki-tracking-form-title">';
								$content .= __('Seguimiento de Pedido', 'chazki');
								$content .= '</h2>';
								$content .= '<form method="get" class="chazki-tracking-form" style="display: inline-block;text-align:center;width:100%">';								
								$content .= '<input type="text" name="chazkitrackid" style="width:100%" class="chazki-tracking-form-field" value="' . $trackid  . '" >';
								$content .= '<br>';
								$content .= '<input name="submit_button" type="submit"  value="' . __('Buscar', 'chazki') . '"  id="chazki_update_button"  class="chazki-tracking-form-submit update_button" />';
								$content .= '</form>';
								$content .= '<br>';
								$content .= '<br>';
								if (empty($trackid)) {
												return wp_kses($content , $allowedTags);
								}																
								$sdk = new ChazkiSdk();

								$response = $sdk->get_tracking($trackid, 'UTC' . get_option('gmt_offset'));

								$descrStatus = array(
												'NEW'          => __('El pedido es recién ingresado al sistema', 'chazki') ,
												'PLANNED'  => __('El pedido paso a planeamiento', 'chazki') ,
												'ASSIGNED'      => __('El pedido es ofrecido para que un Chazki lo acepte', 'chazki') ,
												'GOTO_PICK'      => __('El Chazki va camino a recoger el pedido', 'chazki') ,
												'ARRIVED_PICK'      => __('El Chazki llega al punto de recojo del pedido', 'chazki') ,
												'PICKING'      => __('El Chazki esta recogiendo pedido', 'chazki') ,
												'GOTO_DELIVER'      => __('El Chazki está camino a entregar el pedido', 'chazki') ,
												'ARRIVED_DELIVER'      => __('El Chazki llega al punto de entrega del pedido', 'chazki') ,
												'DELIVERED'  => __('El pedido fue entregado con exito', 'chazki') ,
												'FAILED_PICK'  => __('Falla durante el recojo del pedido', 'chazki') ,
												'FAILED_DROP'       => __('Falla ocurrida durante la entrega del pedido', 'chazki') ,
												'CANCELED'    => __('El pedido fue cancelado', 'chazki') ,
												'PICKUP' => __('El pedido tuvo un fallo de recojo y se vuelve a recoger', 'chazki') ,
												'RETURN' => __('El pedido es retornado', 'chazki') ,
												'REPROGRAMMING' => __('El pedido es reprogramado', 'chazki') ,
								);
								
								if ($response && $response['response'] === 1) {
												$settings = Helper::get_setup_from_settings();
												$content .= "<p>" . $response['timestamp'] . ' - ' . $descrStatus[$response['status']] . '</p>';																								
												$content .= "<p> Verlo en Chazki <a href='https://nintendo". $settings['environment'] .".chazki.com/trackcodeTracking/"  . $trackid . "'>" . $trackid . '</p>';
												
								}
								else {
												$content = $content . "<br>" . __("No se encontró el código de seguimiento", 'chazki');
								}

								
								$content .= '</p>';

								$content .= '</div>';
								$content .= '<style>
															.chazki-tracking-form-title {
																margin-top: 5px;
															}
                              #chazki_update_button{
                                border-radius: 300px;
                                box-shadow: 0 2px 0 0 #337ce3;
                                position: relative;
                                transition: .1s background-color linear;
                                padding: 13px 26px;
                                font-size: 14px;
                                font-weight: 700;
                                font-style: normal;
                                text-transform: uppercase;
                                letter-spacing: .06em;
                                color: #fff;
                                background-color: #1d67d1;
                                border-color: #1d67d1;
                                display: inline-block;
                                width: auto;
                                height: auto;
                                border-width: 0;
                                text-align: center;
                                text-decoration: none;
                                cursor: pointer;
                                appearance: none;
                                line-height: normal;
																margin-top: 5px;
                              }
                              #chazki_update_button:hover{
                                background-color: #337ce3;
                                border-color: #337ce3;
                                box-shadow: 0 2px 0 0 #1d67d1;
                              }
                              #chazki_update_button:focus{
                                outline: none;
                              }
                              .chazki-tracking-form-field{
                                 width: 60%;
                                 margin: 0 auto;
                                 border: none; /* <-- This thing here */
                                 border:solid 1px #ccc;
                                 border-radius: 300px !important;
                              }

                              input.chazki-tracking-form-field:focus{
                                  outline: none;
                              }
                              </style>
                            ';
								return wp_kses($content, $allowedTags);

				}
}
