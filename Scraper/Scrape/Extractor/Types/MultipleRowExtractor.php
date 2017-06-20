<?php

namespace Scraper\Scrape\Extractor\Types;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;
use Scraper\Exception\BadConfigurationException;

/**
 * Class MultipleRowExtractor
 *
 * @package scraper\scrape\extractor\types
 */
class MultipleRowExtractor extends SingleRowExtractor
{

    /**
     * @var string[] Stops crawling after matching hash
     */
    public $stopAtHash = null;

    public $minHashMatch = 1;

    /**
     * {@inheritdoc}
     *
     * @param null $rootElement
     *
     * @return array
     * @throws \Exception
     */
    public function extract($rootElement = null, $exitingRows = null)
    {
        // Make stopAtHash an array if it is not an array
        if (!is_array($this->stopAtHash)) {
            $this->stopAtHash = [$this->stopAtHash];
        }

        $currentUrlNode = $this->crawler->getPage();

        $rootElement = $this->getRootElement($currentUrlNode, $rootElement);

        $rows = $rootElement->findAll(
            'xpath',
            $this->configuration->getRowXPath()
        );

        $results = $this->processRows($exitingRows, $rows);

        return $results;
    }

    /**
     * @param NodeElement     $rootElement
     * @param DocumentElement $currentUrlNode
     * @return mixed
     */
    protected function retryForJavascript(
        DocumentElement $currentUrlNode,
        NodeElement $rootElement = null
    ) {
        // If javascript is enabled then sleep for a second so that the contents
        // that might be loaded would be loaded properly
        // todo : make 1 sec dynamic or check if dom is on ready state

        $retryCount = 0;
        while ($retryCount <= 3 || $rootElement == null) {
            sleep(1);
            $rootElement = $currentUrlNode->find(
                'xpath',
                $this->configuration->getTargetXPath()
            );
            $retryCount++;
        }


        return $rootElement;
    }

    /**
     * @param NodeElement     $rootElement
     * @param DocumentElement $currentUrlNode
     * @return NodeElement
     * @throws \Exception
     */
    protected function getRootElement(
        DocumentElement $currentUrlNode,
        NodeElement $rootElement = null
    ) {
        if ($rootElement == null) {
            $rootElement = $currentUrlNode->find(
                'xpath',
                $this->configuration->getTargetXPath()
            );
        }

        if ($rootElement == null &&
            $this->crawler->javaScriptRequired == true
        ) {
            $rootElement =
                $this->retryForJavascript($currentUrlNode, $rootElement);
        }

        if ($rootElement == null) {
            throw new BadConfigurationException(
                'Multiple Extractor Error : Could not select root element (' .
                $this->configuration->getTargetXPath() .
                ')'
            );
        }

        return $rootElement;
    }

    /**
     * @param $results
     * @param $result
     * @return bool
     */
    protected function checkIfRecordExists($results, $result)
    {
        // Ignore duplicate rows caused by loading of the new records to the
        // same page using ajax call
        // Todo : this has to be replaced by deleting the recorded rows so
        // that new records will always be there and hence no redundancy

        $recordExists = false;
        foreach ($results as $res) {
            if ($res['hash'] == $result['hash']) {
                $recordExists = true;
                break;
            }
        }

        return $recordExists;
    }

    /**
     * @param $exitingRows
     * @param $rows
     * @return array
     */
    protected function processRows($exitingRows, $rows)
    {
        $results = [];

        $counter = 0;
        $hashMatched = 0;
        foreach ($rows as $row) {
            if ($exitingRows > 0 &&
                $counter < $exitingRows &&
                $this->crawler->javaScriptRequired
            ) {
                $counter++;
                continue;
            }

            $result = parent::extract($row);

            if (!count($result)) {
                continue;
            }

            if ($this->checkShouldHalt($result, $hashMatched)) {
                // Todo: remove max page hack
                $this->crawler->maxPages = 1; // Forcefully break the crawling
                return $results;
            }

            $recordExists = $this->checkIfRecordExists($results, $result);
            if ($recordExists) {
                continue;
            }

            $results[] = $result;
            $counter++;
        }

        return $results;
    }

    /**
     * @param $result
     * @param $hashMatched
     * @return bool
     */
    protected function checkShouldHalt($result, $hashMatched)
    {
        if ($this->stopAtHash != null &&
            in_array($result['hash'], $this->stopAtHash)
        ) {
            $hashMatched++;
            if ($hashMatched >= $this->minHashMatch) {
                return true;
            }
        }

        return false;
    }
}
