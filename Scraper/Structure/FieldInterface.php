<?php

namespace Scraper\Structure;

use Behat\Mink\Element\NodeElement;
use Scraper\Scrape\Crawler\BaseCrawler;

interface FieldInterface
{
    public function extractData(
        NodeElement $nodeElement,
        BaseCrawler $baseCrawler = null
    );
}
