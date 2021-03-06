<?php


namespace Scraper\Structure;

use Scraper\Traits\SerializableTrait;

abstract class Field
{
    use SerializableTrait;

    public $name;
    public $xpath;
    public $cssPath;
    public $canBeEmpty = false;

    public function __construct($props = [])
    {
        $this->setAttributes($props);
    }

    public function setAttributes($array = [])
    {
        foreach ($array as $key => $value) {
            if (property_exists($this, $key) && $value !== null) {
                $this->$key = $value;
            }
        }
    }
}
