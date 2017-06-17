<?php

namespace Scraper\Structure;

use Scraper\Traits\SerializableTrait;

class Configuration
{
    use SerializableTrait;
    protected $browserType;

    /** @var Field[] */
    protected $fields = [];

    /**
     * @return mixed
     */
    public function getBrowserType()
    {
        return $this->browserType;
    }

    /**
     * @param mixed $browserType
     */
    public function setBrowserType($browserType)
    {
        $this->browserType = $browserType;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param Field[] $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param Field $field
     */
    public function addField(Field $field)
    {
        $this->fields[] = $field;
    }
}
