<?php

namespace Tests\Unit\Structure;

use Concise\Core\TestCase;
use Scraper\Scrape\ConfigurationManager;
use Scraper\Scrape\Crawler\Types\GeneralCrawler;
use Scraper\Scrape\Extractor\Types\MultipleRowExtractor;

class StructureTest extends TestCase
{
    /** @var  ConfigurationManager */
    protected $configurationManager;

    /** @var MultipleRowExtractor */
    protected $extractor;

    public function setUp()
    {
        parent::setUp();
        $dir = realpath(__DIR__ . '/../../Data');
        $file = $dir . "/structure-test.json";
        $this->configurationManager = ConfigurationManager::getInstance($file);
        $configuration = $this->configurationManager->getConfiguration();
        $configuration->setTargetXPath('//div[@class="parent"]');
        $configuration->setRowXPath('//ul/li');
        $this->configurationManager->save($configuration);

        $crawler = new GeneralCrawler('http://localhost:1349/sample.html');
        $this->extractor = new MultipleRowExtractor($crawler, $configuration);
    }

    public function testLocalhostCanBeAccessed()
    {
        $this->assertContains(
            'This is local test page',
            $this->extractor->crawler->getPage()->getOuterHtml()
        );
    }
}
