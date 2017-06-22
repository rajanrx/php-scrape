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
        $configuration = $this->getMultipleRowConfig();
        $extractor = $this->getExtractor($configuration);
        $data = $extractor->extract();
        foreach ($data as &$row) {
            unset($row['hash']);
        }
        $jsonData =
            json_decode(
                file_get_contents($dir . '/multiple-rows.json'),
                true
            );
        $expectedData = [
            $jsonData[0],
            $jsonData[1],
            $jsonData[2],
        ];
        $this->assertEquals(json_encode($expectedData), json_encode($data));
    }

    public function testCanScrapeMultiplePage()
    {
        $dir = realpath(__DIR__ . '/../../../Data');
        $configuration = $this->getMultipleRowConfig();
        $extractor =
            $this->getExtractor($configuration, '//div[@class="pager"]/a');
        $count = 0;
        $renderedPage = null;
        while (true) {
            if ($count == 0) {
                $renderedPage = $extractor->crawler->getPage();
            } else {
                $renderedPage = $extractor->crawler->getNextPage();
            }
            if ($renderedPage == null) {
                break;
            }
            $data = $extractor->extract();
            $this->assertEquals($count + 1, $data[0]['id']);
            $this->assertEquals($count + 2, $data[1]['id']);
            $this->assertEquals($count + 3, $data[2]['id']);
            $count += 3;
        }
        $jsonData =
            json_decode(
                file_get_contents($dir . '/multiple-rows.json'),
                true
            );
        $this->assertEquals(count($jsonData), $count);
        $historyData = $extractor->crawler->getPageHistory();
        $this->assertEquals(
            'http://localhost:1349/multiple-rows.php',
            $historyData[0]['url']
        );
        $this->assertEquals(
            'http://localhost:1349/multiple-rows.php?page=1',
            $historyData[1]['url']
        );
        $this->assertEquals(
            'http://localhost:1349/multiple-rows.php?page=2',
            $historyData[2]['url']
        );
    }

    public function testCrawlingHaltsIfHashMatches()
    {
        $dir = realpath(__DIR__ . '/../../../Data');
        $jsonData =
            json_decode(
                file_get_contents($dir . '/multiple-rows.json'),
                true
            );
        $configuration = $this->getMultipleRowConfig();
        $extractor =
            $this->getExtractor($configuration);
        $extractor->stopAtHash = [md5(json_encode($jsonData[2]))];

        $data = $extractor->extract();
        $expectedData = [
            $jsonData[0],
            $jsonData[1],
        ];
        foreach ($data as &$row) {
            unset($row['hash']);
        }
        $this->assertEquals(json_encode($expectedData), json_encode($data));

        $extractor->stopAtHash[] = md5(json_encode($jsonData[1]));
        $extractor->minHashMatch = 2;
        $data = $extractor->extract();
        foreach ($data as &$row) {
            unset($row['hash']);
        }
        $this->assertEquals(json_encode([$jsonData[0]]), json_encode($data));
    }

    protected function getMultipleRowConfig()
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

        return $configuration;
    }

    /**
     * @param Configuration $configuration
     * @param null          $nextPageSelector
     * @return MultipleRowExtractor
     */
    protected function getExtractor(
        Configuration $configuration,
        $nextPageSelector = null
    ) {
        $crawler =
            new GeneralCrawler(
                'http://localhost:1349/multiple-rows.php',
                $nextPageSelector
            );
        $extractor = new MultipleRowExtractor($crawler, $configuration);
        $extractor->configuration = $configuration;

        return $extractor;
    }
}
