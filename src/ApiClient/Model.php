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

        return $client->get($endpoint);
    }

    public static function find($endpoint, $id)
    {   
        $client = Client::getInstance();

        return $client->get($endpoint . "/" . $id);
    }

    public static function create($endpoint, $data = [])
    {
        $client = Client::getInstance();

        return $client->post($endpoint, $data);
    }

    public static function update($endpoint, $id, $data = [])
    {
        $client = Client::getInstance();

        return $client->put($endpoint . "/" . $id, $data);
    }

    public static function delete($endpoint, $id)
    {
        $client = Client::getInstance();

        return $client->delete($endpoint . "/" . $id);
    }
}