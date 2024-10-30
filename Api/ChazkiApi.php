<?php
namespace Ecomerciar\Chazki\Api;

class ChazkiApi extends ApiConnector implements ApiInterface {
				const DEV_BASE_URL = 'https://us-central1-chazki-link-dev.cloudfunctions.net/';
				const BETA_BASE_URL = 'https://us-central1-chazki-link-beta.cloudfunctions.net/';
				const PROD_BASE_URL = 'https://us-central1-chazki-link.cloudfunctions.net/';

        /**
         * Class Constructor
         *
         * @return Void
         */
				public function __construct(string $apiKey, string $environment) {
								$this->auth_header = $apiKey;
								$this->environment = $environment;
				}

        /**
         * Use Post API
         *
         * @param string $endpoint
         * @param array $body
         * @param array $headers
         * @return bool|string
         */
				public function post(string $endpoint, array $body = [], array $headers = []) {
								$url = $this->get_base_url() . $endpoint;			
								$headers['Content-Type'] = 'application/json';

								return $this->exec('POST', $url, $body, $headers);
				}

        /**
         * Use Get API
         *
         * @param string $endpoint
         * @param array $body
         * @param array $headers
         * @return bool|string
         */
				public function get(string $endpoint, array $body = [], array $headers = []) {
								$url = $this->get_base_url() . $endpoint;								
								$headers['Content-Type'] = 'application/json';

								if (!empty($body)) {
												$url .= '?' . http_build_query($body);
								}
								return $this->exec('GET', $url, [], $headers);
				}
							
        /**
         * Get Base API Url depending on Plugin Mode: Sandbox | Production
         *
         * @return string
         */
				public function get_base_url() {
								if ($this->environment === '-dev') {
												return self::DEV_BASE_URL;
								} else if ($this->environment === '-beta') {
									return self::BETA_BASE_URL;
								}
								return self::PROD_BASE_URL;
				}
		
}
