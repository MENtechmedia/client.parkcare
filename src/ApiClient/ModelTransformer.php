<?php namespace MENtechmedia\ApiClient;

class ModelTransformer
{
    public static function createModelsFromData($data)
    {
        $collection = [];

        foreach($data as $attributes)
        {
            $collection[] = new Model($attributes);
        }

        return collect($collection);
    }
}