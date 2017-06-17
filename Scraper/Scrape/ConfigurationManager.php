<?php

namespace Scraper\Scrape;

use Scraper\Structure\Configuration;

class ConfigurationManager
{
    /** @var  ConfigurationManager */
    private static $instance;

    /** @var  string */
    private $fileName;

    /** @var  Configuration */
    public $configuration;

    public static function getInstance($fileName)
    {
        if (null === static::$instance) {
            $self = new static();
            $self->fileName = $fileName;
            $self->getOrCreateConfiguration();
            static::$instance = $self;
        }

        return static::$instance;
    }

    protected function getConfiguration()
    {
        $config = file_get_contents($this->fileName);
        if ($config === false) {
            throw new \Exception(
                'Unable to open file ' . $this->fileName
            );
        }
        if (!$this->isJson($config)) {
            throw new \Exception('Invalid JSON file provided');
        }
        $array = json_decode($config, true);
        $this->configuration = new Configuration();
        $this->configuration->constructFromArray($array);

        return $this->configuration;
    }

    protected function createConfiguration()
    {
        // Create empty file
        file_put_contents($this->fileName, '{}');

        return $this->getConfiguration();
    }

    public function getOrCreateConfiguration()
    {
        if (file_exists($this->fileName)) {
            return $this->getConfiguration();
        }

        return $this->createConfiguration();
    }

    public function save()
    {
        return file_put_contents(
            self::$instance->fileName,
            json_encode(
                self::$instance->configuration->toArray(),
                JSON_PRETTY_PRINT
            )
        );
    }

    protected function isJson($str)
    {
        $json = json_decode($str);

        return $json && $str != $json;
    }

    /**
     * is not allowed to call from outside: private!
     */
    private function __construct()
    {
    }

    /**
     * prevent the instance from being cloned
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * prevent from being un-serialized
     *
     * @return void
     */
    private function __wakeup()
    {
    }
}
