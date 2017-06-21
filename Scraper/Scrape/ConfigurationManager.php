<?php

namespace Scraper\Scrape;

use Scraper\Exception\InvalidFileException;
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
        if (static::$instance === null ||
            static::$instance->fileName != $fileName
        ) {
            $self = new static();
            $self->fileName = $fileName;
            $self->getOrCreateConfiguration();
            static::$instance = $self;
        }

        return static::$instance;
    }

    public function getConfiguration()
    {
        if (!file_exists($this->fileName)) {
            throw new InvalidFileException(
                'Unable to open file ' . $this->fileName
            );
        }
        $config = file_get_contents($this->fileName);
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
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * prevent the instance from being cloned
     *
     * @codeCoverageIgnore
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * prevent from being un-serialized
     *
     * @codeCoverageIgnore
     * @return void
     */
    private function __wakeup()
    {
    }
}
