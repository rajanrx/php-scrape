<?php


namespace Scraper\Structure;

use Behat\Mink\Element\NodeElement;
use Scraper\Scrape\Crawler\BaseCrawler;

class DateField extends Field implements FieldInterface
{

    public $format = 'Y-m-d h:i:s';

    public function extractData(
        NodeElement $nodeElement,
        BaseCrawler $baseCrawler = null
    ) {
        return date($this->format, strtotime($nodeElement->getText()));
    }
}
