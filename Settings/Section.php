<?php
namespace Ecomerciar\Chazki\Settings;

/**
 * Chazki Setting Section Main
 */
class Section {

        /**
         * Checks system requirements
         *
         * @return Array Fields Settings for Chazki
         */
				public static function get() {
								$pages = get_pages();
								$pagesOption = array();
								foreach ($pages as $page) {
												$pagesOption[$page
																->ID] = $page->post_title;
								}
								

								$wc_order_statuses = wc_get_order_statuses();
								$wc_order_statuses["0"] = __('Deshabilitar procesamiento automático', 'chazki');

								$settings = array(
												array(
																'title'   => __('Chazki', 'chazki') ,
																'desc'    => __('Configuración del método de envío.', 'chazki') ,
																'type'    => 'title',
																'id'      => 'chazki_shipping_options',
												) ,
												/*array(
																'name'     => __('Tienda', 'chazki') ,
																'type'     => 'text',
																'desc'     => __('Campo referido a la tienda. Puedes obtener este dato desde Chazki.', 'chazki') ,
																'desc_tip' => true,
																'id'       => 'chazki-store-id'
												) ,*/
												array(
																'name'     => __('Sucursal', 'chazki') ,
																'type'     => 'text',
																'desc'     => __('Lugar o Sucursal desde donde se origina el pedido. Puedes obtener este dato desde Chazki. ', 'chazki') ,
																'desc_tip' => true,
																'id'       => 'chazki-branch-id'
												) ,
												array(
																'name'     => __('Api-Key', 'chazki') ,
																'type'     => 'text',
																'id'       => 'chazki-api-key',
																'desc_tip' => true,
																'desc'     => __('Puedes obtener la API Key desde Chazki.', 'chazki') ,
												) ,
												array(
																'name'     => __('Evidencia Física', 'chazki') ,
																'type'     => 'select',
																'desc'     => __('Evidencia física que tendrá el empaque y será entregada al cliente final. ', 'chazki') ,
																'desc_tip' => true,
																'id'       => 'chazki-proof-payment',
																'options' => array(
																				'BOLETA'  => 'Boleta',
																				'FACTURA' => 'Factura'
																)
												) ,
												array(
																'name'    => __('País donde opera', 'chazki') ,
																'type'    => 'select',
																'id'      => 'chazki-country',
																'options' => array(
																				'MX' => 'Mexico',
																				'PE' => 'Perú',
																				'CL' => 'Chile',
																				'CO' => 'Colombia',
																				'AR' => 'Argentina'
																)
												) ,
												array(
																'name' => __('Metodo Pago', 'chazki'),
																'type' => 'select',
																'id'	=> 'chazki-payment-method',
																'options'=> array (
																	'PAGADO'=> 'Pagado',
																	'COD' 	=> 'Efectivo'																	
																)
												),
												array(
																'name'     => __('Estado a procesar', 'chazki') ,
																'type'     => 'select',
																'id'       => 'chazki-process-order-status',
																'desc_tip' => true,
																'desc'     => __('Cuando un pedido tiene este estado, se procesa automáticamente con Chazki.', 'chazki') ,
																'options'  => $wc_order_statuses
												) ,
												/*array(
																'name'      => __('Ambiente', 'chazki') ,
																'type'      => 'select',
																'id'        => 'chazki-environment',
																'desc_tip'  => true,
																'desc'      => __('Puedes utilizar el ambiente Sandbox para realizar pruebas. Asegurate de que la configuración está correcta antes de habilitar Producción.', 'chazki') ,
																'options' => array(
																				'sandbox'    => 'Sandbox',
																				'production' => 'Producción'
																)
												) ,*/
												array(
																'name'    => __('Página de Seguimiento', 'chazki') ,
																'type'    => 'select',
																'id'      => 'chazki-tracking-form-page',
																'desc_tip'  => true,
																'desc'      => __('Selecciona la página que se utilizará por defecto para mostrar el formulario de seguimiento.', 'chazki') ,
																'options' => $pagesOption
												) ,
												array(
																'type' => 'sectionend',
																'id'   => 'chazki_shipping_options'
												) ,
												array(
																'title' => __('Notificaciones', 'chazki') ,
																'desc'  => sprintf(__('Para recibir notificaciones acerca de tus envíos con Chazki, debes crear un webhook dentro de tu panel de Chazki, usa esta URL: <strong>%s</strong> con el método POST.', 'chazki') , get_site_url(null, '/api/chazki-orders')) ,
																'type'  => 'title',
																'id'    => 'chazki_shipping_options_webhook',
												) ,
												array(
																'type' => 'sectionend',
																'id'   => 'chazki_shipping_options_webhook'
												) ,

												array(
													'title' => __('Debug', 'chazki') ,
													'desc'  => sprintf(__('Puede habilitar el debug del plugin para realizar un seguimiento de la comunicación efectuada entre el plugin y la API de Chazki. Podrá ver el registro desde el menú <a href="%s">WooCommerce > Estado > Registros</a>.','chazki') , esc_url(get_admin_url(null, 'admin.php?page=status&tab=logs')) ),
													'type'  => 'title',
													'id'    => 'chazki_shipping_options_debug',
												) ,
												array(
													'name'      => '' ,
													'id'        => 'chazki-debug',
													'type'      => 'checkbox',
													'default'   => 'no',
													'desc'  => __('Habilitar Debug','chazki'),
												) ,
												array(
														'type' => 'sectionend',
														'id'   => 'chazki_shipping_options_debug'
												) 
								);

								return $settings;
				}

}
