<?php

use Scraper\Structure\RegexField;
use Scraper\Structure\TextField;

require_once(__DIR__ . '/../vendor/autoload.php');

$configurationManager = \Scraper\Scrape\ConfigurationManager::getInstance(
    __DIR__ . '/Data/test.json'
);
$configuration = $configurationManager->getConfiguration();
$configuration->setTargetXPath('//div[@class="explore-content"]');
$configuration->setRowXPath('//*[contains(@class,"repo-list")]/li');
$configuration->setFields(
    [
        new TextField(
            [
                'name' => 'repo_name',
            ]
        ),
        new TextField(
            [
                'name' => 'repo_url',
            ]
        ),
        new TextField(
            [
                'name' => 'description',
                'canBeEmpty'=> true
            ]
        ),
        new RegexField(
            [
                'name' => 'stars_today',
            ]
        ),
    ]
);
$configurationManager->save();
print_r($configurationManager->getConfiguration());
//print_r(json_encode($serialized, JSON_PRETTY_PRINT));



