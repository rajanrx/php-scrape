<?php


namespace Tests\Unit\Structure;

use Scraper\Structure\DateField;

class DateFieldTest extends StructureTest
{
    public function setUp()
    {
        parent::setUp();
        $configuration = $this->configurationManager->getConfiguration();
        $configuration->setFields(
            [
                new DateField(
                    [
                        'name'               => 'date',
                        'xpath'              => './/p[@class="created"]',
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
        $this->assertContains('2017-08-05', $data[0]['date']);
        $this->assertContains('2017-08-06', $data[1]['date']);
    }
}
