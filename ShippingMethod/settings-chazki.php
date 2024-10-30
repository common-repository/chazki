<?php
namespace Ecomerciar\Chazki\ShippingMethod;

/**
 * Settings for chazki rate shipping.
 *
 */

defined('ABSPATH') || exit;

global $wpdb;
$categories= array();
$categories['-1'] = 'None';
$categoriesDB = $wpdb->get_results("select t.term_id, t.name from {$wpdb->prefix}terms t inner join {$wpdb->prefix}term_taxonomy tt on tt.term_id = t.term_id and tt.taxonomy='product_cat';", OBJECT);       
if (sizeof($categoriesDB) != 0) {
	foreach($categoriesDB as $cat)
	{
		$categories[$cat->term_id] =$cat->name;
	}
}


$settings = array(
				'title' => array(
								'title' 			=> __('Chazki', 'chazki') ,
								'type' 				=> 'text',
								'description' => __('Permite a tus clientes recibir sus pedidos con Chazki', 'chazki') ,
								'default' 		=> __('Chazki', 'chazki') ,
								'desc_tip' 		=> true,
				) ,
				'cost' => array(
								'title' 						=> __('Cost', 'chazki') ,
								'type' 							=> 'text',
								'placeholder' 			=> '',
								'description' 			=> __('Introduce un costo fijo por envío.', 'chazki') ,
								'default'						=> '0',
								'desc_tip' 					=> true,
								'sanitize_callback' => array(
												$this,
												'sanitize_cost'
								) ,
				) ,
				'category' => array(
					'title' 						=> __('Category', 'chazki') ,
					'type' 							=> 'multiselect',					
					'placeholder' 			=> '',
					'description' 			=> __('Seleccione', 'chazki') ,							
					'desc_tip' 					=> true,		
					'options' 		=>  $categories
				) ,
				'dimension' => array(
					'title' 						=> __('Dimensiones', 'chazki') ,
					'type' 							=> 'multiselect',
					'placeholder' 			=> '',
					'description' 			=> __('Seleccione', 'chazki') ,							
					'desc_tip' 					=> true,		
					'options' 		=>  array(
						'None '=> 'None','XS' => 'XS', 'S' => 'S', 'M' => 'M', 'L' => 'L', 'XL' => 'XL'
					)
				) ,
				'service' => array(
								'title' 			=> __('Servicio', 'chazki') ,
								'type' 				=> 'select',
								'desc_tip' 		=> true,
								'description' => __('Dependiendo del tipo de servicio deseado puede ser: Express: El pedido será enviado el mismo día con un tiempo máximo de entrega de 4 hrs. (radio máx. 20 km) 	<br/> Next Day: El pedido será enviado el siguiente día en un horario abierto de 9:00 a 18:00 hrs.', 'chazki') ,
								'options' 		=> array(
												'REGULAR' 			=> 'Regular',
												'EXPRESS' 			=> 'Express',
												'NEXT DAY' 			=> 'Next Day',
												'SAME DAY' 			=> 'Same Day',
												'PROGRAMADO' 	=> 'Programado',
												'PROGRAMADO_1' 	=> 'Programado 1',
												'PROGRAMADO_2' 	=> 'Programado 2',
												'PROGRAMADO_3' 	=> 'Programado 3',												

								)
				) ,
);

return $settings;
