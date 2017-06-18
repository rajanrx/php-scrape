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

        if (count($matches) == 1) {
            return $matches[0][1];
        }

        $results = [];
        foreach ($matches as $match) {
            if (empty($match[1])) {
                continue;
            }
            $results[] = $match[1];
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
