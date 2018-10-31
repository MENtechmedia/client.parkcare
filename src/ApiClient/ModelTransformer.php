<?php namespace MENtechmedia\ApiClient;

class ModelTransformer
{
    public static function createModelsFromData($data, $endpoint)
    {
        $collection = [];

        foreach($data as $attributes)
        {
            $collection[] = new Model($attributes);
        }

        return collect($collection);
    }
}