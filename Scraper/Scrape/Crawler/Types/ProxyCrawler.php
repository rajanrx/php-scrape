<?php

namespace Scraper\Scrape\Crawler\Types;

use Behat\Mink\Driver\GoutteDriver;
use Scraper\Proxy\Structure\Proxy;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Session;
use Goutte\Client;

class ProxyCrawler extends GeneralCrawler
{
    /**
     * Sets Proxy in the browser driver to allow anonymous scraping
     *
     * @param Proxy $proxy
     *
     * @return mixed|void
     * @throws \Exception
     */
    public function setProxy(Proxy $proxy)
    {

        $sessionName = md5($proxy->getUrl());

        $this->browser->resetSessions();

        if ($this->browser->hasSession($sessionName)) {
            $this->browser->setDefaultSessionName($sessionName);

            return;
        }

        $driver = $this->browser->getSession()->getDriver();
        switch (get_class($driver)) {
            case 'Behat\Mink\Driver\GoutteDriver':
                /* @var $driver  GoutteDriver */
                $client = new Client();
                $guzzle = $client->getClient();
                $guzzle->setDefaultOption('proxy', $proxy->getUrl());
                $client->setClient($guzzle);

                $goutteDriver = new GoutteDriver($client);
                $this->browser->registerSession(
                    $sessionName,
                    new Session($goutteDriver)
                );
                $this->browser->setDefaultSessionName($sessionName);
                break;

            case 'Behat\Mink\Driver\Selenium2Driver':
                /* @var $driver Selenium2Driver */

                // Todo : use other files than pac file
                // Currently it does only support pac file
                if (empty($proxy->pacFile)) {
                    throw new \Exception('Pac file/url is required.');
                }

                // @see https://code.google.com/p/selenium/wiki/JsonWireProtocol#Proxy_JSON_Object
                $driver->setDesiredCapabilities(
                    [
                        "proxy" => [
                            "proxyType"          => "pac",
                            "proxyAutoconfigUrl" => $proxy->pacFile,
                        ],
                    ]
                );

                $this->browser->stopSessions();
                $this->browser->registerSession(
                    $sessionName,
                    new Session($driver)
                );
                $this->browser->setDefaultSessionName($sessionName);
                break;

            default:
                throw new \Exception(
                    'Error : Proxy configuration is not implemented for class ' .
                    get_class($driver) .
                    ''
                );
        }
    }
}