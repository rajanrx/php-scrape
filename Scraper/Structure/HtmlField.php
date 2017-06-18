<?php


namespace Scraper\Structure;

use Behat\Mink\Element\NodeElement;
use Scraper\Scrape\Crawler\BaseCrawler;

class HtmlField extends Field implements FieldInterface
{

    public function extractData(
        NodeElement $nodeElement,
        BaseCrawler $baseCrawler = null
    ) {
        return $nodeElement->getOuterHtml();
    }
}
