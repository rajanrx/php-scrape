<?php

namespace Scraper\Scrape\Extractor;


use Scraper\Scrape\Crawler\BaseCrawler;


/**
 * Abstract class for extractor
 *
 * Class BaseExtractor
 * @package Scraper\Scrape\Extractor
 */
abstract class BaseExtractor{

    /**
     * @var BaseCrawler Crawler object for crawling urls
     */
    public $crawler;

    /**
     * @var String Json file defining rules for extracting data
     */
    public $rules;

    /**
     * Initialises extractor
     * @param BaseCrawler $crawler
     * @param String      $rulePath
     *
     * @throws \Exception
     */
    function __construct(BaseCrawler $crawler, $rulePath) {
        $this->crawler = $crawler;

        $rules = file_get_contents($rulePath);

        if($rules === false){
            throw new \Exception("Extractor Error : Invalid rule path ");
        }

        $this->rules = json_decode($rules);
    }

    /**
     * Returns class name
     * @return string
     */
    public static function className(){
        return get_called_class();
    }


    /**
     * Extracts data from crawled markup
     * @return mixed
     */
    abstract public function extract();
}