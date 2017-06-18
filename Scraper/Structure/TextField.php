<?php


namespace Scraper\Structure;

use Behat\Mink\Element\NodeElement;
use Scraper\Scrape\Crawler\BaseCrawler;

class TextField extends Field implements FieldInterface
{
    public $property;

    public function extractData(
        NodeElement $nodeElement,
        BaseCrawler $baseCrawler = null
    ) {
        if (!$this->property) {
            return $nodeElement->getText();
        }

        return $nodeElement->getAttribute($this->property);
    }
}
