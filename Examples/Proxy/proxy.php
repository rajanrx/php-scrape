<?php
/**
 * Created by PhpStorm.
 * User: rajan
 * Date: 11/07/15
 * Time: 11:42 AM
 */

require_once('/Applications/XAMPP/xamppfiles/htdocs/ireview/php-scrape/vendor/autoload.php');
require_once('UltraProxy.php');

use Scraper\Proxy\ProxyFactory;
use Scraper\Scrape\Crawler\Types\GeneralCrawler;


$reviewFactory = new GeneralCrawler('https://api.ipify.org?format=json');
$proxies = ProxyFactory::getInstance()->getProxy(new UltraProxy());

print_r($proxies);


foreach ($proxies as $proxy) {
    try{
        $reviewFactory->setProxy($proxy);
        echo $proxy->anonymity." ".$proxy->getUrl()." : ".$reviewFactory->getPage()->getContent() . "\n";
    }
    catch(\Exception $ex){
        echo $ex->getMessage()."\n";
    }
}