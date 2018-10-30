<?php namespace MENtechmedia\ApiClient;

use GuzzleHttp;

class Client
{
    private $baseUrl;
    private $client;

    private static $instance = null;

    public function __construct($baseUrl, $client_id, $client_secret, $username, $password)
    {
        $this->client = $this->getClient();
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
}