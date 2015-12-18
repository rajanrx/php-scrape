<?php

use Scraper\Proxy\Structure\Proxy;
use Scraper\Scrape\Crawler\Types\GeneralCrawler;
use Scraper\Scrape\Extractor\Types\SingleRowExtractor;

require_once(__DIR__ . '/../vendor/autoload.php');

$proxy = new Proxy();
$proxy->pacFile = 'https://proxymesh.com/static/us-ca.pac';

$crawler   = new GeneralCrawler('https://github.com/trending',null,true);
$crawler->setProxy($proxy);

$path      = __DIR__ . "/Data/git-repo.json";
$extractor = new SingleRowExtractor($crawler, $path);
$data = $extractor->extract();
print_r($data);