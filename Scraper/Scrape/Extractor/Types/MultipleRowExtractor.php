<?php

namespace Scraper\Scrape\Extractor\Types;

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

        if ($rootElement == null) {
            $rootElement = $currentUrlNode->find(
                'xpath',
                $this->rules->extraction->resultXPaths[0]
            );
        }

        // If javascript is enabled then sleep for a second so that the contents
        // that might be loaded would be loaded properly
        // todo : make 1 sec dynamic or check if dom is on ready state
        if ($rootElement == null &&
            $this->crawler->javaScriptRequired == true
        ) {
            $retryCount = 0;
            while ($retryCount <= 3 || $rootElement == null) {
                sleep(1);
                $rootElement = $currentUrlNode->find(
                    'xpath',
                    $this->rules->extraction->resultXPaths[0]
                );
                $retryCount++;
            }
        }

        if ($rootElement == null) {
            throw new \Exception(
                'Multiple Extractor Error : Could not select root element'
            );
        }

        $rows = $rootElement->findAll(
            'xpath',
            $this->rules->extraction->rowXPaths[0]
        );

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

            if ($this->stopAtHash != null &&
                in_array($result['hash'], $this->stopAtHash)
            ) {
                $hashMatched++;
                if ($hashMatched >= $this->minHashMatch) {
                    $this->crawler->maxPages =
                        1; // Forcefully break the crawling
                    break;
                }
                continue;
            }


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

            if ($recordExists) {
                continue;
            }

            $results[] = $result;
            $counter++;
        }

        return $results;
    }
}
