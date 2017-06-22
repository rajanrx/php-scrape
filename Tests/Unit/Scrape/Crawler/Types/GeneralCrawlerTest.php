<?php

namespace Tests\Unit\Scrape\Crawler\Types;

use Concise\Core\TestCase;
use Scraper\Scrape\Crawler\Types\GeneralCrawler;

class GeneralCrawlerTest extends TestCase
{
    public function testCanGetClassName()
    {
        $this->assertEquals(
            'Scraper\Scrape\Crawler\Types\GeneralCrawler',
            GeneralCrawler::className()
        );
    }
}
