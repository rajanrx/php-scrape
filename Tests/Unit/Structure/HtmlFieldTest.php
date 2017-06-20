<?php


namespace Tests\Unit\Structure;

use Scraper\Structure\HtmlField;

class HtmlFieldTest extends StructureTest
{
    public function setUp()
    {
        parent::setUp();
        $configuration = $this->configurationManager->getConfiguration();
        $configuration->setFields(
            [
                new HtmlField(
                    [
                        'name' => 'htmlData',
                        'xpath' => './/p[@class="created"]',
                    ]
                ),
            ]
        );
        $this->extractor->configuration = $configuration;
    }

    public function testExtractData()
    {
        $data = $this->extractor->extract();
        $this->assertCount(2, $data);
        $this->assertContains(
            '<p class="created">5th August 2017</p>',
            $data[0]['htmlData']
        );
        $this->assertContains(
            '<p class="created">6th August 2017</p>',
            $data[1]['htmlData']
        );
    }
}
