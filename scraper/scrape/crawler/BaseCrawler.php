<?php
/**
 * Created by PhpStorm.
 * User: rajan
 * Date: 23/06/15
 * Time: 8:21 AM
 */

namespace scraper\scrape\crawler;


use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\Element;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Goutte\Client;
use GuzzleHttp\Subscriber\Cache\CacheSubscriber;
use scraper\proxy\structure\Proxy;

/**
 * Class BaseCrawler
 * @package scraper\scrape\crawler
 */
abstract class BaseCrawler {

    /**
     * @var
     */
    public $currentUrl;
    /**
     * @var null
     */
    public $nextPageSelector;
    /**
     * @var bool
     */
    public $javaScriptRequired;
    /**
     * @var
     */
    public $terminateNextPage;
    /**
     * @var int
     */
    public $maxPages = 0;


    /**
     * @var Mink
     */
    protected $browser;
    /**
     * @var array
     */
    protected $pageHistory = array();

    /**
     * @param $currentUrl
     * @param null $nextPageSelector
     * @param bool $javaScriptRequired
     * @param Session $driver
     */
    function __construct($currentUrl, $nextPageSelector = null, $javaScriptRequired = false , Session $driver = null) {
        $this->currentUrl         = $currentUrl;
        $this->nextPageSelector   = $nextPageSelector;
        $this->javaScriptRequired = $javaScriptRequired;

        $this->getBrowser($driver);
        $this->setPageHistory();
    }

    /**
     * @return string
     */
    public static function className(){
        return get_called_class();
    }

    /**
     * @param $relayNetwork
     */
    public function setRelayNetwork($relayNetwork){

        $this->currentUrl  = $relayNetwork.$this->currentUrl;
    }

    /**
     * @return \Behat\Mink\Element\DocumentElement
     */
    abstract public  function getPage();

    /**
     * @param null $nextPageSelector
     *
     * @return mixed
     */
    abstract public function  getNextPage($nextPageSelector = null);

    /**
     * @param null $nextPageSelector
     *
     * @return mixed
     */
    abstract public function setNextPage($nextPageSelector = null);

    /**
     * @param Proxy $proxy
     *
     * @return mixed
     */
    abstract public function setProxy(Proxy $proxy);

    /**
     * @return mixed
     */
    abstract public function getPageHistory();

    /**
     *
     */
    protected function setPageHistory(){
        $this->pageHistory[] = [
            'url' => $this->currentUrl
        ];
    }

    /**
     * @param Element $element
     *
     * @return bool
     */
    protected function checkEnabled(Element $element){

        return false;
    }

    /**
     * @param Session $driver
     */
    private function getBrowser(Session $driver = null){

        if($driver != null){

            $this->browser = new Mink([
                'custom' => $driver
            ]);

            $this->browser->setDefaultSessionName('custom');
            return;
        }

        $client = new Client();
        $guzzle = $client->getClient();
        CacheSubscriber::attach($guzzle,[
            //'storage' => new CacheStorage(new FilesystemCache('/tmp/crawlCache')),
            //'validate' => false,
        ]);

        $client->setClient($guzzle);

        // init Mink and register sessions
        $this->browser = new Mink([
            'goutte'    => new Session(new GoutteDriver($client)),
            'selenium2' => new Session(new Selenium2Driver('firefox',[
                "permissions.default.image" => 2
            ])),
        ]);

        if(!$this->javaScriptRequired){
            $this->browser->setDefaultSessionName('goutte');
            return;
        }

        $this->browser->setDefaultSessionName('selenium2');

    }
}