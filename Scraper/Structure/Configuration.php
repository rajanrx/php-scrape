<?php

namespace Scraper\Structure;

use Scraper\Traits\SerializableTrait;

class Configuration
{
    use SerializableTrait;
    protected $browserType;

    protected $targetXPath;
    protected $targetCssPath;
    protected $rowXPath;
    protected $rowCssPath;


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

    /**
     * @return mixed
     */
    public function getTargetCssPath()
    {
        return $this->targetCssPath;
    }

    /**
     * @param mixed $targetCssPath
     */
    public function setTargetCssPath($targetCssPath)
    {
        $this->targetCssPath = $targetCssPath;
    }

    /**
     * @return mixed
     */
    public function getRowCssPath()
    {
        return $this->rowCssPath;
    }

    /**
     * @param mixed $rowCssPath
     */
    public function setRowCssPath($rowCssPath)
    {
        $this->rowCssPath = $rowCssPath;
    }

    /**
     * @return mixed
     */
    public function getTargetXPath()
    {
        return $this->targetXPath;
    }

    /**
     * @param mixed $targetXPath
     */
    public function setTargetXPath($targetXPath)
    {
        $this->targetXPath = $targetXPath;
    }

    /**
     * @return mixed
     */
    public function getRowXPath()
    {
        return $this->rowXPath;
    }

    /**
     * @param mixed $rowXPath
     */
    public function setRowXPath($rowXPath)
    {
        $this->rowXPath = $rowXPath;
    }
}
