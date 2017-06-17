<?php

namespace Scraper\Scrape\Crawler;

use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\Element;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Goutte\Client;
use GuzzleHttp\Subscriber\Cache\CacheSubscriber;
use Scraper\Proxy\Structure\Proxy;

/**
 * Class BaseCrawler
 *
 * @package scraper\scrape\crawler
 */
abstract class BaseCrawler
{

    /**
     * @var null Current url of the website
     */
    public $currentUrl;
    /**
     * @var null Next page selector
     */
    public $nextPageSelector;
    /**
     * @var bool Set true if javascript is required. Default false
     */
    public $javaScriptRequired;
    /**
     * @var null Not used
     */
    public $terminateNextPage;
    /**
     * @var int Maximum number of pages to crawl
     */
    public $maxPages = 0;


    /**
     * @var Mink Driver for browsing web
     */
    protected $browser;
    /**
     * @var array History of url being crawled
     */
    protected $pageHistory = [];

    /**
     * Initialize crawler
     * Setting Javascript enabled to true automatically selects Selenium2 as a
     * default web browser driver
     *
     * @param         $currentUrl
     * @param null    $nextPageSelector
     * @param bool    $javaScriptRequired
     * @param Session $driver
     */
    public function __construct(
        $currentUrl,
        $nextPageSelector = null,
        $javaScriptRequired = false,
        Session $driver = null
    ) {
        $this->currentUrl = $currentUrl;
        $this->nextPageSelector = $nextPageSelector;
        $this->javaScriptRequired = $javaScriptRequired;

        $this->setBrowser($driver);
        $this->setPageHistory();
    }

    /**
     * Return class name
     *
     * @return string
     */
    public static function className()
    {
        return get_called_class();
    }

    /**
     * Sets relay network ahead of the url. Useful when using TOR Relay networks
     *
     * @param $relayNetwork
     */
    public function setRelayNetwork($relayNetwork)
    {

        $this->currentUrl = $relayNetwork . $this->currentUrl;
    }

    /**
     * Gets current page
     *
     * @param bool $forceReload
     *
     * @return \Behat\Mink\Element\DocumentElement
     */
    abstract public function getPage($forceReload = false);

    /**
     * Gets Next page if pagination selector is provided
     *
     * @param null $nextPageSelector
     *
     * @return mixed
     */
    abstract public function getNextPage($nextPageSelector = null);

    /**
     * Sets next page using provided selector
     *
     * @param null $nextPageSelector
     *
     * @return mixed
     */
    abstract public function setNextPage($nextPageSelector = null);

    /**
     * Sets Proxy in the browser driver to allow anonymous scraping
     *
     * @param Proxy $proxy
     *
     * @return mixed
     */
    abstract public function setProxy(Proxy $proxy);

    /**
     * Return visited page history
     *
     * @return mixed
     */
    abstract public function getPageHistory();

    /**
     * Sets page history
     */
    protected function setPageHistory()
    {
        $this->pageHistory[] = [
            'url' => $this->currentUrl,
        ];
    }

    /**
     * Checks if next page selector is enabled
     * Not implemented properly yet. Default is false which means the next page
     * selector is enabled all the time
     *
     * @param Element $element
     *
     * @return bool
     */
    protected function checkEnabled(Element $element)
    {

        return false;
    }

    /**
     * Sets the browser driver depending on the javascript select parameter or
     * injected browser driver
     *
     * @param Session $driver
     */
    private function setBrowser(Session $driver = null)
    {

        if ($driver != null) {
            $this->browser = new Mink(
                [
                    'custom' => $driver,
                ]
            );

            $this->browser->setDefaultSessionName('custom');

            return;
        }

        $client = new Client();
        $guzzle = $client->getClient();
        CacheSubscriber::attach(
            $guzzle,
            [
                //'storage' => new CacheStorage(new FilesystemCache('/tmp/crawlCache')),
                //'validate' => false,
            ]
        );

        $client->setClient($guzzle);

        // init Mink and register sessions
        $this->browser = new Mink(
            [
                'goutte'    => new Session(new GoutteDriver($client)),
                'selenium2' => new Session(
                    new Selenium2Driver(
                        'firefox',
                        [
                            "permissions.default.image" => 2,
                        ]
                    )
                ),
            ]
        );

        if (!$this->javaScriptRequired) {
            $this->browser->setDefaultSessionName('goutte');

            return;
        }

        $this->browser->setDefaultSessionName('selenium2');
    }
}
