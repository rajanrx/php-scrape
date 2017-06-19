# php-scrape [![Build Status](https://travis-ci.org/rajanrx/php-scrape.svg?branch=master)](https://travis-ci.org/rajanrx/php-scrape)
A scraping framework written in PHP

## About PHP-scrape
Php Scrape is a basic scraping framework for PHP based on configuration first
concept. i.e once implemented changes should be made on configuration file as far
as possible avoiding need for code update/addition.

## Getting Started
The easiest way to use Php-Scrape is via Composer.
```
composer require --dev rajanrx/php-scrape
```

You need to create configuration file to start scraping. You can do it either by 
creating a [config JSON](https://github.com/rajanrx/php-scrape/blob/master/Examples/Data/git-repo.json) 
file or via [using php](https://github.com/rajanrx/php-scrape/blob/master/Examples/ConfigGenerator.php)
 (Highly recommended as its easier to maintain and scale ) to generate one.

Once you have a configuration file you can start scraping by writing few lines of 
code

```php
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
```

will return result like
```
Array
(
    [0] => Array
        (
            [repo_name] => ecrmnn / collect.js
            [repo_url] => https://github.com/ecrmnn/collect.js
            [description] => Convenient and dependency free wrapper for working with arrays and objects
            [stars_today] => 493
            [hash] => 0e5522aa1ad972b50dc93b8c9f3cc6c8
        )

    [1] => Array
        (
            [repo_name] => samdeeplearning / The-Terrible-Deep-Learning-List
            [repo_url] => https://github.com/samdeeplearning/The-Terrible-Deep-Learning-List
            [description] => 15 working examples to get you started with Deep Learning without learning any of the math.
            [stars_today] => 311
            [hash] => bd6587020e6072938f441ed22a64375b
        )

    [2] => Array
        (
            [repo_name] => tensorflow / models
            [repo_url] => https://github.com/tensorflow/models
            [description] => Models built with TensorFlow
            [stars_today] => 271
            [hash] => de60007b8cc8c296918c94e6a525c645
        )
  ...
```
As easy as that. Docs in detail will be updated soon. 
Interested contributors are hearty welcome.

## Security Vulnerabilities

If you discover a security vulnerability, please send an e-mail to
Rajan Rauniyar at rajanrauniyar@gmail.com.
All security vulnerabilities will be promptly addressed.

## License

This framework is open-sourced software licensed under the 
[MIT license](http://opensource.org/licenses/MIT). 
