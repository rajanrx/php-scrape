<?php

use Scraper\Scrape\Crawler\Types\GeneralCrawler;

require_once('/Applications/XAMPP/xamppfiles/htdocs/ireview/php-scrape/vendor/autoload.php');

$crawler = new GeneralCrawler('https://github.com/trending');

$path = __DIR__."/Data/git-repo.json";
$extractor = new \Scraper\Scrape\Extractor\Types\MultipleRowExtractor($crawler,$path);

$data = $extractor->extract();

print_r($data);