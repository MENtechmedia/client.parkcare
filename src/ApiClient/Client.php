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

    public static function getInstance() 
    {
		if(!self::$instance) {
			throw new ApiException("Client not initialized, run init() first");
		}
		return self::$instance;
    }
    
    public function call($method, $endpoint, $data = [])
    {
        try {
            $response = $this->client->request($method, $endpoint, [
                'json' => $data
            ]);
        } catch (ApiException $e){

        }

        $decodedResponse = $this->decodeResponseData($response->getBody()->getContents());

        $models = ModelTransformer::createModelsFromData($decodedResponse);

        return $models;
    }

    private function decodeResponseData($data) {
		return json_decode($data, true);
	}

    public function get($endpoint) 
    {
        return $this->call('GET', $endpoint);
    }

    public function put($endpoint, $data) 
    {
        return $this->call('PUT', $endpoint, $data);
    }
    
    public function post($endpoint, $data) 
    {
        return $this->call('GET', $endpoint, $data);
    }
    
    public function delete($endpoint) 
    {
        return $this->call('DELETE', $endpoint);
	}
}