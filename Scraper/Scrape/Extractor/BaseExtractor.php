<?php

namespace Scraper\Scrape\Extractor;

use Scraper\Scrape\Crawler\BaseCrawler;
use Scraper\Structure\Configuration;

/**
 * Abstract class for extractor
 *
 * Class BaseExtractor
 *
 * @package Scraper\Scrape\Extractor
 */
abstract class BaseExtractor
{

    /**
     * @var BaseCrawler Crawler object for crawling urls
     */
    public $crawler;

    /**
     * @var Configuration Configuration for extracting data
     */
    public $configuration;

    /**
     * Initialises extractor
     *
     * @param BaseCrawler   $crawler
     * @param Configuration $configuration
     * @throws \Exception
     *
     */
    public function __construct(BaseCrawler $crawler, Configuration $configuration)
    {
        $this->crawler = $crawler;
        $this->configuration = $configuration;
    }

    /**
     * Returns class name
     *
     * @return string
     */
    public static function className()
    {
        return get_called_class();
    }


    /**
     * Extracts data from crawled markup
     *
     * @return mixed
     */
    abstract public function extract();
}
