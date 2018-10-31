<?php namespace MENtechmedia\ApiClient;

class Model
{
    private $client;
    private $endpoint;
    private $attributes;

    public function __construct($endpoint, $attributes)
    {
        $this->endpoint = $endpoint;
        $this->client = Client::getInstance();
    }

    public static function all($endpoint)
    {
        return self::getContentsFromResponse($client->get($endpoint));
    }

    public static function byId($endpoint, $id)
    {   
        return self::getContentsFromResponse($client->get($endpoint . "/" . $id));
    }

    public static function create($endpoint, $data = [])
    {
        
    }

    private static function getContentsFromResponse($response)
    {
        return $response->getBody()->getContents();
    }


}