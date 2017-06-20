<?php

namespace Scraper\Structure;

use Behat\Mink\Element\NodeElement;
use Scraper\Scrape\Crawler\BaseCrawler;

class AnchorField extends Field implements FieldInterface
{
    const HREF = 'href';
    public $convertRelativeUrl = true;

    public function extractData(
        NodeElement $nodeElement,
        BaseCrawler $baseCrawler = null
    ) {
        $url = $nodeElement->getAttribute(self::HREF);
        if ($this->convertRelativeUrl) {
            return $this->getFullUrl($url, $baseCrawler);
        }

        return $url;
    }

    protected function getFullUrl($url, BaseCrawler $baseCrawler = null)
    {
        if (!$baseCrawler) {
            return $url;
        }
        if (substr(trim($url), 0, 1) == '/') {
            $parse = parse_url($baseCrawler->currentUrl);

            return "{$parse['scheme']}://{$parse['host']}{$url}";
        }

        return $url;
    }
}
