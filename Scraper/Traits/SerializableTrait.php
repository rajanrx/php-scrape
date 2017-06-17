<?php

namespace Scraper\Traits;


trait SerializableTrait
{
    public function toArray()
    {
        $array = get_object_vars($this);
        $array['phpClass'] = get_class($this);
        unset($array['_parent'], $array['_index']);
        array_walk_recursive(
            $array,
            function (&$property) {
                if (is_object($property) &&
                    method_exists($property, 'toArray')
                ) {
                    $property = $property->toArray();
                }
            }
        );

        return $array;
    }

    public static function getObjectFromJson($json)
    {
        if (!self::isJson($json)) {
            throw new \Exception('Invalid JSON file provided');
        }
        $array = json_decode($json);

        return self::getObjectRecursively($array);
    }

    protected static function getObjectRecursively($element)
    {
        if (is_scalar($element)) {
            throw new \Exception('Provided element is scalar');
        }
        $object = [];
        if (is_array($element) && array_key_exists('phpClass', $element)) {
            $class = $element['phpClass'];
            $object = new $class();
        } else {
            if (is_object($element) && property_exists($element, 'phpClass')) {
                $class = $element->phpClass;
                $object = new $class();
            }
        }

        foreach ($element as $key => $value) {
            if ($value != null && !is_scalar($value)) {
                if (is_array($object)) {
                    $object[$key] = self::getObjectRecursively($value);
                } else {
                    $object->$key = self::getObjectRecursively($value);
                }
            } elseif (property_exists($object, $key)) {
                $object->$key = $value;
            }
        }

        return $object;
    }

    protected static function isJson($str)
    {
        $json = json_decode($str);

        return $json && $str != $json;
    }
}