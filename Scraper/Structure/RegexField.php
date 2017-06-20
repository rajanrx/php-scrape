<?php


namespace Scraper\Structure;

use Behat\Mink\Element\NodeElement;
use Scraper\Scrape\Crawler\BaseCrawler;

class RegexField extends Field implements FieldInterface
{
    public $regex;

    /**
     * Parses regular expression
     *
     * @param $string
     *
     * @return array|null
     */
    private function parseRegex($string)
    {
        preg_match_all($this->regex, $string, $matches, PREG_SET_ORDER);
        if (!count($matches)) {
            return null;
        }

        if (count($matches[0]) == 1) {
            return $matches[0][1];
        }

        $results = [];
        foreach ($matches[0] as $k => $match) {
            if (empty($match) || $k == 0) {
                continue;
            }
            $results[] = $match;
        }

        if (count($results) == 1) {
            return $results[0];
        }

        return $results;
    }

    public function extractData(
        NodeElement $nodeElement,
        BaseCrawler $baseCrawler = null
    ) {
        return $this->parseRegex($nodeElement->getText());
    }
}
