<?php


namespace Tests\Unit\Structure;

use Scraper\Structure\TextField;

class TextFieldTest extends StructureTest
{
    public function setUp()
    {
        parent::setUp();
        $configuration = $this->configurationManager->getConfiguration();
        $configuration->setFields(
            [
                new TextField(
                    [
                        'name'  => 'header',
                        'xpath' => './/h1',
                    ]
                ),
                new TextField(
                    [
                        'name'     => 'headerId',
                        'xpath'    => './/h1',
                        'property' => 'id',
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
        $this->assertContains('first row', $data[0]['header']);
        $this->assertContains('second row', $data[1]['header']);

        $this->assertEquals('first-header', $data[0]['headerId']);
        $this->assertEquals('second-header', $data[1]['headerId']);
    }
}
