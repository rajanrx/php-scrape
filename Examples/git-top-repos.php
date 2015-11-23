<?php

use Scraper\Scrape\Crawler\Types\GeneralCrawler;
use Scraper\Scrape\Extractor\Types\MultipleRowExtractor;

require_once(__DIR__ . '/../vendor/autoload.php');

$crawler   = new GeneralCrawler('https://github.com/trending');
$path      = __DIR__ . "/Data/git-repo.json";
$extractor = new MultipleRowExtractor($crawler, $path);

$data = $extractor->extract();
print_r($data);