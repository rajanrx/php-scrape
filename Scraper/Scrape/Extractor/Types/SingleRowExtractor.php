<?php

namespace Scraper\Scrape\Extractor\Types;

use Scraper\Scrape\Extractor\BaseExtractor;
use Scraper\Structure\DateField;
use Scraper\Structure\HtmlField;
use Scraper\Structure\RegexField;

/**
 * Class SingleRowExtractor
 *
 * @package scraper\scrape\extractor\types
 */
class SingleRowExtractor extends BaseExtractor
{
    /**
     * {@inheritdoc}
     *
     * @param null $rootElement
     *
     * @return array
     * @throws \Exception
     */
    public function extract($rootElement = null)
    {

        $fields = [];

        if ($rootElement == null) {
            $rootElement = $this->crawler->getPage()->find(
                'xpath',
                $this->configuration->getTargetXPath()
            );
        }

        if ($rootElement == null) {
            throw new \Exception(
                'Single Extractor Error : Could not select root element'
            );
        }

        foreach ($this->configuration->getFields() as $field) {
            if (isset($field->xpath)) {
                $element = $rootElement->find('xpath', $field->xpath);
                if ($element != null) {
                    $fields[$field->name] = $element->getText();

                    if ($field instanceof HtmlField) {
                        $fields[$field->name] =
                            $element->getOuterHtml();
                    }

                    if (isset($field->property)) {
                        $fields[$field->name] =
                            $element->getAttribute($field->property);
                        if ($field->property == 'href') {
                            if (substr(trim($fields[$field->name]), 0, 1) == '/'
                            ) {
                                $parse = parse_url($this->crawler->currentUrl);
                                $fields[$field->name] =
                                    $parse['scheme'] .
                                    '://' .
                                    $parse['host'] .
                                    $fields[$field->name];
                            }
                        }
                    }

                    if ($field instanceof RegexField) {
                        $fields[$field->name] = $this->parseRegex(
                            $fields[$field->name],
                            $field->regex
                        );
                    }

                    if ($field instanceof DateField) {
                        $fields[$field->name] = date(
                            $field->format,
                            strtotime($fields[$field->name])
                        );
                    }
                }
            }
        }

        if (count($fields)) {
            $fields['hash'] = md5(json_encode($fields));
        }

        return $fields;
    }

    /**
     * Parses regular expression
     *
     * @param $string
     * @param $regex
     *
     * @return array|null
     */
    private function parseRegex($string, $regex)
    {
        preg_match_all($regex, $string, $matches, PREG_SET_ORDER);

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
}
