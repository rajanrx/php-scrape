<?php


namespace Tests\Unit\Structure;

use Scraper\Structure\AnchorField;

class AnchorFieldTest extends StructureTest
{
    public function setUp()
    {
        parent::setUp();
        $configuration = $this->configurationManager->getConfiguration();
        $configuration->setFields(
            [
                new AnchorField(
                    [
                        'name'               => 'relativeUrl',
                        'xpath'              => './/a',
                        'convertRelativeUrl' => false,
                    ]
                ),
                new AnchorField(
                    [
                        'name'  => 'fullUrl',
                        'xpath' => './/a',
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
        $this->assertEquals('/first.com', $data[0]['relativeUrl']);
        $this->assertEquals('/second.com', $data[1]['relativeUrl']);

        $this->assertEquals(
            'http://localhost/first.com',
            $data[0]['fullUrl']
        );
        $this->assertEquals(
            'http://localhost/second.com',
            $data[1]['fullUrl']
        );
    }
}
