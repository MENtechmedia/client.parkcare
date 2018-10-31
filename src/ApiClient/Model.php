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

        $client->get($endpoint);
    }
}