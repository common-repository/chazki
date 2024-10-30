<?php
namespace Ecomerciar\Chazki\Api;

use Ecomerciar\Chazki\Helper\Helper;

/**
 * Abstract API Class
 */
abstract class ApiConnector {
      /**
       * Executes API Request
       *
       * @param string $method
       * @param string $url
       * @param array $body
       * @param array $headers
       * @return string
       */
				protected function exec(string $method, string $url, array $body, array $headers) {
								if (isset($headers['Content-Type']) && $headers['Content-Type'] === 'application/json') {
												$body = json_encode($body, JSON_UNESCAPED_UNICODE);
								}
                Helper::log( '==============================================>' );
                Helper::log( $url );
                Helper::log( $headers );
                Helper::log( 'Request > ' );
                Helper::log( $body );
								
                if ($method === "POST"){
                  $result = wp_remote_post($url , array(
                    'headers'     => $headers,
                    'body'        => $body,
                    'method'      => 'POST',
                    'data_format' => 'body',
                    'timeout'     => 20,
                    ));        
                }	else {                  
                  $result = wp_remote_get($url, array(
                    'headers' => $headers,
                    'timeout'     => 10,
                  ));
                }					
                if ( ! is_wp_error($result) ){
                  $bodyResult = wp_remote_retrieve_body( $result );                                                         
                }	 else {
                  $bodyResult = '{}';
                }
                
                Helper::log( 'Response > ' );
                Helper::log( $bodyResult );

								return json_decode($bodyResult, true);
				}

        /**
         * Executes Post Request
         *
         * @param string $endpoint
         * @param array $body
         * @param array $headers
         * @return string
         */
				public function post(string $endpoint, array $body = [], array $headers = []) {
								$url = $this->get_base_url() . $endpoint;
								return $this->exec('POST', $url, $body, $headers);
				}

        /**
         * Executes Get Request
         *
         * @param string $endpoint
         * @param array $body
         * @param array $headers
         * @return string
         */
				public function get(string $endpoint, array $body = [], array $headers = []) {
								$url = $this->get_base_url() . $endpoint;
								if (!empty($body)) $url .= '?' . http_build_query($body);
								return $this->exec('GET', $url, [], $headers);
				}

        /**
         * Executes Put Request
         *
         * @param string $endpoint
         * @param array $body
         * @param array $headers
         * @return string
         */
				public function put(string $endpoint, array $body = [], array $headers = []) {
								$url = $this->get_base_url() . $endpoint;
								return $this->exec('PUT', $url, $body, $headers);
				}

        /**
         * Executes Delete Request
         *
         * @param string $endpoint
         * @param array $body
         * @param array $headers
         * @return string
         */
				public function delete(string $endpoint, array $body = [], array $headers = []) {
								$url = $this->get_base_url() . $endpoint;
								return $this->exec('DELETE', $url, $body, $headers);
				}

        /**
         * Add Get Params to URL
         *
         * @param string $url
         * @param array $params
         * @return string
         */
				protected function add_params_to_url($url, $params) {
								if (strpos($url, '?') !== false) {
												$url .= '&' . $params;
								}
								else {
												$url .= '?' . $params;
								}
								return $url;
				}

				public abstract function get_base_url();
}
