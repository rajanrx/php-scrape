<?php

namespace Scraper\Traits;


trait SerializableTrait
{
    public function toArray()
    {
        $array = get_object_vars($this);
        $array['phpClass'] = get_class($this);
        unset($array['_parent'], $array['_index']);
        array_walk_recursive($array, function (&$property) {
            if (is_object($property) && method_exists($property, 'toArray')) {
                $property = $property->toArray();
            }
        });
        return $array;
    }

    public function constructFromArray($array)
    {
        array_walk_recursive($array, function (&$property) {
            print_r($property);
            if (is_object($property) && method_exists($property, 'toArray')) {
                $property = $property->toArray();
            }
        });
    }
}