<?php

namespace Scraper\Scrape\Extractor\Types;

use Scraper\Exception\BadConfigurationException;
use Scraper\Scrape\Extractor\BaseExtractor;
use Scraper\Structure\FieldInterface;

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
            throw new BadConfigurationException(
                'Single Extractor Error : Could not select root element'
            );
        }

        foreach ($this->configuration->getFields() as $field) {
            if (!$field instanceof FieldInterface) {
                throw new BadConfigurationException('Field should be of type FieldInterface');
            }
            if (isset($field->xpath)) {
                $element = $rootElement->find('xpath', $field->xpath);
                if ($element == null) {
                    continue;
                }
                $fields[$field->name] = $field->extractData($element, $this->crawler);
            }
        }

        if (count($fields)) {
            $fields['hash'] = md5(json_encode($fields));
        }

        return $fields;
    }
}
