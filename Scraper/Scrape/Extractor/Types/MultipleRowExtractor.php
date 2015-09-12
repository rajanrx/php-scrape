<?php
/**
 * Created by PhpStorm.
 * User: rajan
 * Date: 25/06/15
 * Time: 12:48 PM
 */

namespace Scraper\Scrape\Extractor\Types;


/**
 * Class MultipleRowExtractor
 * @package scraper\scrape\extractor\types
 */
class MultipleRowExtractor extends SingleRowExtractor {

    /**
     * @var null Stops crawling after matching hash
     */
    public $stopAtHash = null;

    /**
     * {@inheritdoc}
     * @param null $rootElement
     *
     * @return array
     * @throws \Exception
     */
    public function extract($rootElement = null) {

        $currentUrlNode = $this->crawler->getPage();

        if ($rootElement == null) {
            $rootElement = $currentUrlNode->find('xpath', $this->rules->extraction->resultXPaths[0]);
        }

        // If javascript is enabled then sleep for a second so that the contents that might be loaded would be loaded properly
        // todo : make 1 sec dynamic or check if dom is on ready state
        if ($rootElement == null && $this->crawler->javaScriptRequired == true) {

            $retryCount = 0;
            while ($retryCount <= 3 || $rootElement == null) {
                sleep(1);
                $rootElement = $currentUrlNode->find('xpath', $this->rules->extraction->resultXPaths[0]);
                $retryCount++;
            }
        }

        if ($rootElement == null) {
            throw new \Exception('Multiple Extractor Error : Could not select root element');
        }

        $rows = $rootElement->findAll('xpath', $this->rules->extraction->rowXPaths[0]);

        $results = array();

        foreach ($rows as $row) {
            $result = parent::extract($row);
            if ($this->stopAtHash != null && $this->stopAtHash == $result['hash']) {
                $this->crawler->maxPages = 1; // Forcefully break the crawling
                break;
            }
            if (!count($result)) {
                continue;
            }
            $results[] = $result;
        }

        return $results;

    }
}