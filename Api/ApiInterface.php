<?php
namespace Ecomerciar\Chazki\Api;

/**
 * API Interface Class
 */
interface ApiInterface {
				public function get(string $endpoint, array $body = [], array $headers = []);
				public function post(string $endpoint, array $body = [], array $headers = []);
				public function put(string $endpoint, array $body = [], array $headers = []);
				public function delete(string $endpoint, array $body = [], array $headers = []);
}
