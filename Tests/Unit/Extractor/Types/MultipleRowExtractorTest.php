<?php

namespace Tests\Unit\Extractor\Types;

use Concise\Core\TestCase;
use Scraper\Scrape\ConfigurationManager;
use Scraper\Scrape\Crawler\Types\GeneralCrawler;
use Scraper\Scrape\Extractor\Types\MultipleRowExtractor;
use Scraper\Structure\Configuration;
use Scraper\Structure\TextField;

class MultipleRowExtractorTest extends TestCase
{
    /**
     * @expectedException   \Scraper\Exception\BadConfigurationException
     * @expectedExceptionMessage Multiple Extractor Error : Could not select
     *     root element
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

    public function testCanScrapeMultipleRows()
    {
        $dir = realpath(__DIR__ . '/../../../Data');
        $file = $dir . "/structure-test.json";
        $configurationManager = ConfigurationManager::getInstance($file);
        $configuration = $configurationManager->getConfiguration();
        $configuration->setTargetXPath('//div[@class="rows"]');
        $configuration->setRowXPath('//ul/li');
        $configuration->setFields(
            [
                new TextField(
                    [
                        'name'     => 'id',
                        'xpath'    => './/div[@class="element"]',
                        'property' => 'id',
                    ]
                ),
                new TextField(
                    [
                        'name'  => 'name',
                        'xpath' => './/h1',
                    ]
                ),
            ]
        );

        $extractor = $this->getExtractor($configuration);
        $data = $extractor->extract();
        foreach ($data as &$row) {
            unset($row['hash']);
        }
        $expectedData = file_get_contents($dir . '/multiple-rows.json');
        $this->assertJsonStringEqualsJsonFile(
            $dir . '/multiple-rows.json',
            json_encode($data)
        );
    }

    public function testCanScrapeMultiplePage()
    {
    }

    /**
     * @param Configuration $configuration
     * @return MultipleRowExtractor
     */
    protected function getExtractor(Configuration $configuration)
    {
        $crawler =
            new GeneralCrawler('http://localhost:1349/multiple-rows.php');
        $extractor = new MultipleRowExtractor($crawler, $configuration);
        $extractor->configuration = $configuration;

        return $extractor;
    }
}
