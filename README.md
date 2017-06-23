# PHP Scrape [![Build Status](https://travis-ci.org/rajanrx/php-scrape.svg?branch=master)](https://travis-ci.org/rajanrx/php-scrape)
A simple, easy to use, scalable scraping framework written in PHP

## About PHP Scrape
Php Scrape is a basic scraping framework for PHP based on configuration first
concept. i.e once implemented changes should be made on configuration file as far
as possible avoiding need for code update/addition. Also, you can extend/Customize
this framework to any level or use components (Extractor, Crawler) separately if 
you just need to use them.

Following are the key points which you can use/expect in future:

- [x] Scrape in console or browser
- [x] Use hash to escape duplicate scraping (or halt further scraping)
- [x] Generate editable configuration file using PHP code
- [x] Ability to extend own scraping components
- [ ] Add complete wiki for general and advance usage instructions
- [x] Add test coverage for command line scraping (> 80%)
- [ ] Add test coverage for Javascript scraping
- [ ] Allow use of proxy to scrape anonymously
- [ ] Generate automated integration test for scraping to ensure data integrity 

## Why Need For yet another git repo ?
One of the biggest problem in scraping data is the source gets changed and we 
have to update our codebase to get it working. As the codebase increases it is
harder to maintain and even annoying looking for the place to update if someone
new to codebase has to maintain it. Also different projects has their own unique
requirements (made even harder by varieties/complexity of data sources) which might 
not be addressed by lots of libraries for not being generic enough. 
So in order to help facilitate developers tackle these problems, I have tried to
come up with a generic, flexible solution that might help them to write easily
configurable, maintainable and (extend/customize)able scraping projects.

## Getting Started
The easiest way to use PHP Scrape is via Composer.
```
composer require rajanrx/php-scrape:^1.1.1
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

// Grab the crawler
$crawler = new GeneralCrawler('https://github.com/trending');

// Get config using configuration manager
$path = __DIR__ . "/Data/git-repo.json";
$configurationManager =
    \Scraper\Scrape\ConfigurationManager::getInstance($path);

// Run extractor (Multiple) as we need to grab multiple rows for Github 
// trending repos
$extractor = new MultipleRowExtractor(
    $crawler, $configurationManager->getConfiguration()
);
$data = $extractor->extract();

// Print retrieved data
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
As simple as that. 
Docs in detail will be updated soon.Meanwhile until the doc is not available please 
see [Multi Row Extractor Test](https://github.com/rajanrx/php-scrape/blob/master/Tests/Unit/Extractor/Types/MultipleRowExtractorTest.php) 
to figure out how you can scrape paginated records.

Please let me know if you have any suggestions to make this codebase better. I am
happy to assist if you get stuck on your scraping project :). Feel free to ping me.
Interested contributors are welcome.

## License

This framework is open-sourced software licensed under the 
[MIT license](http://opensource.org/licenses/MIT). 

If you are happy and want to buy me a coffee 
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ND8GEY5QKW6TG)
then why not :). 
