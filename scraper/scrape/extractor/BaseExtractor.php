<?php

namespace Scraper\Scrape\Extractor;
use Scraper\Scrape\Crawler\BaseCrawler;


/**
 * Created by PhpStorm.
 * User: rajan
 * Date: 25/06/15
 * Time: 12:38 PM
 */

abstract class BaseExtractor{

    /**
     * @var BaseCrawler
     */
    public $crawler;

    /**
     * @var String
     */
    public $rules;

    function __construct(BaseCrawler $crawler, $rulePath) {
        $this->crawler = $crawler;

        $rules = file_get_contents($rulePath);

        if($rules === false){
            throw new \Exception("Extractor Error : Invalid rule path ");
        }

        $this->rules = json_decode($rules);
    }

    public static function className(){
        return get_called_class();
    }


    abstract public function extract();
}