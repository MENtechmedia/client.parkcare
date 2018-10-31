<?php namespace MENtechmedia\ApiClient;

class Model
{
    private $endpoint;
    private $attributes;

    public function __construct($endpoint, $attributes)
    {
        $this->endpoint = $endpoint;
    }

    public static function all($endpoint)
    {
        $client = Client::getInstance();

        return self::getContentsFromResponse($client->get($endpoint));
    }

    public static function byId($endpoint, $id)
    {   
        $client = Client::getInstance();

        return self::getContentsFromResponse($client->get($endpoint . "/" . $id));
    }

    public static function create($endpoint, $data = [])
    {
        $client = Client::getInstance();

        return self::getContentsFromResponse($client->post($endpoint, $data));
    }

    private static function getContentsFromResponse($response)
    {
        return $response->getBody()->getContents();
    }


}