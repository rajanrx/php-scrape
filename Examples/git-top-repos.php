<?php

use Scraper\Scrape\Crawler\Types\GeneralCrawler;
use Scraper\Scrape\Extractor\Types\MultipleRowExtractor;

require_once(__DIR__ . '/../vendor/autoload.php');
date_default_timezone_set('UTC');

$crawler = new GeneralCrawler('https://github.com/trending');
$path = __DIR__ . "/Data/git-repo.json";
$configurationManager =
    \Scraper\Scrape\ConfigurationManager::getInstance($path);
$extractor = new MultipleRowExtractor(
    $crawler, $configurationManager->getConfiguration()
);

$data = $extractor->extract();
print_r($data);