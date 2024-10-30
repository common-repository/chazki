<?php
namespace Ecomerciar\Chazki\Sdk;

use Ecomerciar\Chazki\Api\ChazkiApi;
use Ecomerciar\Chazki\Helper\Helper;

/**
 * Chazki SDK Main Class
 */
class ChazkiSdk {

				private $storeId;

        /**
         * Constructor Method
         *
         * @return Void
         */
				public function __construct() {
								$this->chazki_settings = Helper::get_setup_from_settings();
								$this->api = new ChazkiApi($this->chazki_settings['api-key'], $this->chazki_settings['environment']);
				}

        /**
         * Generates Unique ID for Tracking Codes
         *
         * @param int $order_id Order Identifier
         * @return String Unique ID
         */
				public function generate_track_code($order_id) {
                $prefix = $order_id . '-' ;
								return strtoupper(uniqid($prefix, false));
				}

        /**
         * Checks if AutoProcess is enabled on Chazki Settings
         *
         * @return bool
         */
				public function is_auto_process() {
								if ($this->chazki_settings['process-order-status'] === "0") {
												return false;
								}
								return true;
				}

        /**
         * Get Auto Process Status from Settings
         *
         * @return string
         */
				public function get_auto_process_status() {
								return $this->chazki_settings['process-order-status'];
				}

      /**
       * Process Order with Chazki API
       *
       * @param WC_Order $order Order to process
       * @return string
       */
				public function process_order(\WC_Order $order) {
								$endpoint = "uploadClientOrders";
								$customer = Helper::get_customer_from_order($order);
								$itemList = array();
								$items = Helper::get_items_from_order($order);
                $costShipping = Helper::get_shipping_cost($order);
								$grouped_items = Helper::group_items($items);

								$service = Helper::get_shipping_service($order);
                $countriesPackages = ["PE","MX"];
                $productDescTot = "";
                $qtyProd = 0;
                $weightProd = 0;
                $priceProd = 0;
                $weight_unit = get_option('woocommerce_weight_unit');
                  foreach ($grouped_items as $item) {
                          array_push($itemList, [
                                  "clientPackageID"     => strval($item['id']),
                                  "envelope"            => '',
                                  "weight"              => $item['weight'],
                                  "weightUnit"          => $weight_unit,
                                  "size"                => $item['chazki-product-size'],
                                  "quantity"            => $item['quantity'],              
                                  "name"                => $item['description'],
                                  "currency"            => "",
                                  "unitaryProductPrice" => $item['price'],
                          ]);
                          $productDescTot .= $item['description'] . ' ;';
                          $qtyProd += $item['quantity'];
                          $weightProd += $item['weight'] * $item['quantity'];
                          $priceProd += $item['price'] * $item['quantity'];
                  }
                $productDescTot = substr_replace($productDescTot ,"", -1);

								

                $nivel_2 = "";
                $nivel_3 = "";
                $nivel_4 = "";
                
                switch ($this->chazki_settings['country']) {
                  case "MX":
                      $nivel_2 = $customer['province'];
                      $nivel_3 = $customer['locality'];
                      $nivel_4 = $customer['locality'];
                      break;
                  case "PE":
                      $nivel_2 = $customer['province'];
                      $nivel_3 = $customer['province'];
                      $nivel_4 = $customer['locality'];
                      break;                  
                }
                $trackCode = $this->generate_track_code($order->get_id());
                $store_address     = get_option( 'woocommerce_store_address' );
                $store_address_2   = get_option( 'woocommerce_store_address_2' );
                $store_city        = get_option( 'woocommerce_store_city' );
                $store_postcode    = get_option( 'woocommerce_store_postcode' );


                $dataOrder = array(                                
                  "trackCode"                   => $trackCode,
                  "paymentProofID"              => $this->chazki_settings['proof-payment'],
                  "paymentMethodID"             => $this->chazki_settings['payment-method'],                  
                  "serviceID"                   => $service,
                  "packageEnvelope"             => 'Caja',
                  "packageWeight"               => $weightProd /1000,
                  "packageQuantity"             => $qtyProd,
                  "packageSizeID"               => count($itemList)> 0 ? $itemList[0]['size']: 'XS',
                  "shipmentPrice"               => $costShipping,
                  "productDescription"          => $productDescTot,
                  "productPrice"                => $priceProd,
                  "reverseLogistic"             => "NO",
                  "crossdocking"                => "NO",
                  "pickUpBranchID"              => '',
                  "pickUpAddress"               => $store_address ? $store_address : '-',
                  "pickUpPostalCode"            => $store_postcode,
                  "pickUpAddressReference"      => '-',
                  "pickUpPrimaryReference"      => $store_address_2? $store_address_2 : '-',
                  "pickUpSecondaryReference"    => $store_city,
                  "pickUpNotes"                 => '',
                  "pickUpContactDocumentTypeID" => 'RUC',
                  "pickUpContactDocumentNumber" => '999999999',
                  "pickUpContactEmail"          => '',
                  "dropBranchID"                => '',
                  "dropAddress"                 => $customer['full_address'],
                  "dropPostalCode"              => $customer['cp'],                                
                  "dropAddressReference"        => "NS",
                  "dropPrimaryReference"        => $nivel_4,
                  "dropSecondaryReference"      => $customer['province'],
                  "dropNotes"                   => $customer['extra_info'],                                
                  "dropContactName"             => $customer['first_name'] . ' ' . $customer['last_name'],
                  "dropContactPhone"            => $customer['phone'],                                
                  "dropContactDocumentTypeID"   => 'DNI',
                  "dropContactDocumentNumber"   => '99999999',
                  "dropContactEmail"            =>  $customer['email'],
                  "providerID"                  => strval($order->get_id()),
                  "providerName"                => 'WOOCOMM'
                );

                
                if ( in_array( $this->chazki_settings['country'], $countriesPackages  )){
                  $extraData = array( 'packages' => $itemList);
                  $dataOrder = $dataOrder + $extraData;
                }


								$data = [
                           "enterpriseKey" => $this->chazki_settings['api-key'],
                           "orders" => array_values(array( $dataOrder ))
                          ];
                Helper::log($data);
                

								$res = $this
												->api
												->post($endpoint, $data);
                
                if ( !empty($res) ){
                  $trackData = array('trackCode' => $trackCode);
                  $res = $res + $trackData;  
                }                
								return $res;
				}

        /**
         * Get Tracking Status from TrackId
         *
         * @param string $trackid Tracking Identifier from Order
         * @param string $timezone Defines Timezone
         * @return string
         */
				public function get_tracking($trackid, $timezone = "") {
								$endpoint = "fnTrackOrderChazkiPosition";								

								$body = [
                          "code" => $trackid,
                          "enterpriseKey" => $this->chazki_settings['api-key'],                          
                        ];
								$header = [];
								$res = $this
												->api
												->get($endpoint, $body, $header);
								return $res;
				}

        /**
         * Get Label from Tracking Code
         * 
         * @param string $trackid Tracking Identifier from Order
         * @return string
         */
        public function get_label($trackid){
          $endpoint = "createPDFByOrders";
          $body = [
            "filters" => array( 'trackCodes' => [$trackid]),
            "enterpriseKey" => $this->chazki_settings['api-key']
          ];
          $header = array('Content-Type' => 'application/json');
          $res = $this
                  ->api
                  ->post($endpoint, $body, $header);          
          return isset($res['file']) && substr($res['file'], 0, 4) === "http"? $res['file'] : '';
        }        
}
