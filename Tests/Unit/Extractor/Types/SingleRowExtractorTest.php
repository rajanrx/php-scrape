<?php

namespace Tests\Unit\Extractor\Types;

use Concise\Core\TestCase;
use Scraper\Scrape\ConfigurationManager;
use Scraper\Scrape\Crawler\Types\GeneralCrawler;
use Scraper\Scrape\Extractor\Types\SingleRowExtractor;
use Scraper\Structure\Configuration;

class SingleRowExtractorTest extends TestCase
{
    /**
     * @expectedException   \Scraper\Exception\BadConfigurationException
     * @expectedExceptionMessage Single Extractor Error : Could not select root
     *     element
     */
    public function testWillThrowExceptionIfRootElementNotFound()
    {
        $dir = realpath(__DIR__ . '/../../../Data');
        $file = $dir . "/structure-test.json";
        $configurationManager = ConfigurationManager::getInstance($file);
        $configuration = $configurationManager->getConfiguration();
        $configuration->setTargetXPath('//div[@class="nonExistingClass"]');

        $extractor = $this->getExtractor($configuration);
        $extractor->extract();
    }

    /**
     * @expectedException   \Scraper\Exception\BadConfigurationException
     * @expectedExceptionMessage Field should be of type FieldInterface
     */
    public function testWillThrowExceptionIfFieldTypeIsNotFieldInterface()
    {
        $dir = realpath(__DIR__ . '/../../../Data');
        $file = $dir . "/invalid-field.json";
        $configurationManager = ConfigurationManager::getInstance($file);
        $configuration = $configurationManager->getConfiguration();

        $extractor = $this->getExtractor($configuration);
        $extractor->extract();
    }

    protected function getExtractor(Configuration $configuration)
    {
        $crawler = new GeneralCrawler('http://localhost:1349/sample.html');
        $extractor = new SingleRowExtractor($crawler, $configuration);
        $extractor->configuration = $configuration;

        return $extractor;
    }
}
