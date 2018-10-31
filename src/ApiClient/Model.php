<?php namespace MENtechmedia\ApiClient;

class Model
{
    public $attributes;

    public function __get($property)
    {
        if(property_exists($property, $this)){
            return $this->$property;
        }
        
        return $this->attributes[$property];
    }

    public function __construct($attributes)
    {
        $this->fill($attributes);
    }

    public function fill($attributes) 
    {
		$this->attributes = $attributes;
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