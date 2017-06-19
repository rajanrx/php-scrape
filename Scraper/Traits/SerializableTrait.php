<?php

namespace Scraper\Traits;

use Scraper\Exception\BadConfigurationException;
use Scraper\Exception\InvalidFileException;

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
        if (self::isJson($json) === false) {
            throw new InvalidFileException('Invalid JSON file provided');
        }
        $array = json_decode($json);

        return self::getObjectRecursively($array);
    }

    protected static function getObjectRecursively($element)
    {
        if (is_scalar($element)) {
            throw new \Exception('Provided element is scalar');
        }
        $object = self::getObject($element);

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


    /**
     * @param $element
     * @return array
     * @throws BadConfigurationException
     */
    protected static function getObject($element)
    {
        $object = [];
        if (is_array($element) && array_key_exists('phpClass', $element)) {
            $namespace = $element['phpClass'];
            $object = self::getObjectFromClassNamespace($namespace);
        } else {
            if (is_object($element) && property_exists($element, 'phpClass')) {
                /** @noinspection PhpUndefinedFieldInspection */
                $namespace = $element->phpClass;
                $object = self::getObjectFromClassNamespace($namespace);
            }
        }

        return $object;
    }

    protected static function getObjectFromClassNamespace($namespace)
    {
        if (!class_exists($namespace)) {
            throw new BadConfigurationException(
                'Provided class does not exists'
            );
        }
        return new $namespace();
    }
}
