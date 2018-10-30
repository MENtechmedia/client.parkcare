<?php namespace MENtechmedia\ApiClient;

use GuzzleHttp;

class Client
{
    private $baseUrl;
    private $client;

    private static $instance = null;

    public function __construct($baseUrl, $client_id, $client_secret, $username, $password)
    {
        $this->client = $this->getClient($baseUrl);
    }

    public static function init($baseUrl, $client_id, $client_secret, $username, $password)
    {
        if(!self::$instance) {
			self::$instance = new self($baseUrl, $client_id, $client_secret, $username, $password);
		}
		return self::$instance;

    }

    private function getClient($baseUrl)
    {
        return new GuzzleHttp\Client([
            'base_uri' => $baseUrl,
			'headers' => [
				'Accept' => 'application/json',
				'Content-Type' => 'application/x-www-form-urlencoded',
			]
		]);
    }

    public function get($endpoint) {
        return $this->call('GET', $endpoint);
    }

	public function put($endpoint) {
		return $this->call('PUT', $endpoint, $data, $includes);
    }
    
	public function post($endpoint) {
		return $this->call('POST', $endpoint, $data, $includes);
    }
    
	public function delete($endpoint) {
		return $this->call('DELETE', $endpoint);
	}
}