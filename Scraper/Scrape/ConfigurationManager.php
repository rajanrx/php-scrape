<?php

namespace Scraper\Scrape;

use Scraper\Structure\Configuration;

class ConfigurationManager
{
    /** @var  ConfigurationManager */
    private static $instance;

    /** @var  string */
    protected $fileName;

    /** @var  Configuration */
    protected $configuration;

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

    public function getConfiguration()
    {
        $config = file_get_contents($this->fileName);
        if ($config === false) {
            throw new \Exception(
                'Unable to open file ' . $this->fileName
            );
        }

        $this->configuration = Configuration::getObjectFromJson($config);

        return $this->configuration;
    }

    public function createConfiguration()
    {
        $configuration = new Configuration();
        file_put_contents(
            $this->fileName,
            json_encode($configuration->toArray(), JSON_PRETTY_PRINT)
        );

        return $this->getConfiguration();
    }

    public function getOrCreateConfiguration()
    {
        if (file_exists($this->fileName)) {
            return $this->getConfiguration();
        }

        return $this->createConfiguration();
    }

    public function save(Configuration $configuration = null)
    {
        $configuration = $configuration ?: $this->configuration;
        return file_put_contents(
            $this->fileName,
            json_encode(
                $configuration->toArray(),
                JSON_PRETTY_PRINT
            )
        );
    }

    /**
     * is not allowed to call from outside: private!
     */
    private function __construct()
    {
    }

    /**
     * @param Configuration $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
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
