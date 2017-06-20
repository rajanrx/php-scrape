<?php


namespace Tests\Unit\Structure;

use Scraper\Structure\RegexField;

class RegexFieldTest extends StructureTest
{
    public function setUp()
    {
        parent::setUp();
        $configuration = $this->configurationManager->getConfiguration();
        $configuration->setFields(
            [
                new RegexField(
                    [
                        'name'  => 'regexFilteredData',
                        'xpath' => './/span',
                        'regex' => '/(\d*)\s[regex check]/'
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
        $this->assertEquals('4', $data[0]['regexFilteredData']);
        $this->assertEquals('5', $data[1]['regexFilteredData']);
    }
}
