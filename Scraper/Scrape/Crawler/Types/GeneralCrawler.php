<?php
/**
 * Created by PhpStorm.
 * User: rajan
 * Date: 23/06/15
 * Time: 9:06 AM
 */

namespace Scraper\Scrape\Crawler\Types;


use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Session;
use Goutte\Client;
use Scraper\Proxy\Structure\Proxy;
use Scraper\Scrape\Crawler\BaseCrawler;


/**
 * Class GeneralCrawler
 * @package Scraper\Scrape\Crawler\Types
 */
class GeneralCrawler extends BaseCrawler{

    /**
     * {@inheritdoc}
     * @return \Behat\Mink\Element\DocumentElement
     */
    public function getPage($forceReload = false) {

        $currentUrl = $this->currentUrl;

        $session = $this->browser->getSession();

        // Do not reload the page if the page has been already loaded unless it is forced
        if(count($this->pageHistory) > 1 && $session->getCurrentUrl() == $currentUrl && $forceReload == false){
            return $session->getPage();
        }

        $session->visit($currentUrl);
        return $session->getPage();
    }

    /**
     * {@inheritdoc}
     * @param null $nextPageSelector
     *
     * @return \Behat\Mink\Element\DocumentElement|null
     * @throws \Exception
     */
    public function getNextPage($nextPageSelector = null) {

        $isNextPage = $this->setNextPage($nextPageSelector);
        if(!$isNextPage){
            return null;
        }
        return $this->getPage();
    }

    /**
     * {@inheritdoc}
     * @param null $nextPageSelector
     *
     * @return bool
     * @throws \Exception
     */
    public function setNextPage($nextPageSelector = null){

        if($nextPageSelector != null){
            $this->nextPageSelector = $nextPageSelector;
        }

        if($this->nextPageSelector == null){
            throw new \Exception('Crawler Error : Next page selector not provided');
        }

        $nextButton = $this->getPage()->find('xpath',$this->nextPageSelector);

        if($nextButton == null || $this->checkEnabled($nextButton) || ($this->maxPages > 0 && sizeof($this->pageHistory) >= $this->maxPages) ){
            return false;
        }

        try{
            $nextButton->click();
            $this->setPageHistory();
            $this->currentUrl = $this->browser->getSession()->getCurrentUrl();
        }
        catch(\Exception $ex){
            if(is_a($ex,'Behat\Mink\Exception\UnsupportedDriverActionException')){
                return false;
            }
            throw $ex;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     * @param Proxy $proxy
     *
     * @return mixed|void
     * @throws \Exception
     */
    public function setProxy(Proxy $proxy) {

        $sessionName = md5($proxy->getUrl());

        $this->browser->resetSessions();

        if($this->browser->hasSession($sessionName)){
            $this->browser->setDefaultSessionName($sessionName);
            return;
        }

        $driver = $this->browser->getSession()->getDriver();
        switch(get_class($driver)){
            case 'Behat\Mink\Driver\GoutteDriver':

                /* @var $driver  GoutteDriver */
                $client = new Client();
                $guzzle = $client->getClient();
                $client->setClient($guzzle);
                $guzzle->setDefaultOption('proxy', $proxy->getUrl());

                $goutteDriver = new GoutteDriver($client);
                $this->browser->registerSession($sessionName,new Session($goutteDriver));
                $this->browser->setDefaultSessionName($sessionName);
                break;

            default:
                throw new \Exception ('Error : Proxy configuration is not implemented for class ' .get_class($driver). '');
        }
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function getPageHistory() {
        return $this->pageHistory;
    }
}