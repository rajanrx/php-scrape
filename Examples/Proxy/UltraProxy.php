<?php


/**
 * Created by PhpStorm.
 * User: rajan
 * Date: 27/06/15
 * Time: 10:28 AM
 */

use Scraper\Proxy\Generator\ProxyGenerator;
use Scraper\Proxy\Structure\Proxy;
use Scraper\Scrape\Crawler\Types\GeneralCrawler;
use Scraper\Scrape\Extractor\Types\MultipleRowExtractor;

class UltraProxy extends ProxyGenerator{

    const URL = "http://www.ultraproxies.com/high-anonymous.html";
    const RULE_FILE = "/../data/ultra-proxy.json";

    protected static $waitTimeOutInSeconds = 2;

    public function generateProxy() {

        $crawler = new GeneralCrawler(self::URL,null,true);
        $extractor = new MultipleRowExtractor($crawler,__DIR__.self::RULE_FILE);

        $results = $extractor->extract();
        $proxies = array();
        foreach($results as $result){

            $result['working'] = parent::checkProxy($result['ip'],$result['port']);
            if(!$result['working']){
                continue;
            }
            $proxies[] = $this->getProxy($result);
        }

        return $proxies;
    }

    private function getProxy($item) {

        $proxy = new Proxy([
            'ip'        => $item['ip'],
            'port'      => $item['port'],
            'speed'     => $item['speed'],
            'types'     => array(),
            'type'      => null,
            'working'   => $item['working'] ? true : false,
            'country'   => $item['country'],
            'anonymity' => $item['anonymity']
        ]);

        return $proxy;
    }
}