<?php
/**
 * Created by PhpStorm.
 * User: rajan
 * Date: 25/06/15
 * Time: 12:48 PM
 */

namespace scraper\scrape\extractor\types;


/**
 * Class MultipleRowExtractor
 * @package scraper\scrape\extractor\types
 */
class MultipleRowExtractor extends SingleRowExtractor{

    /**
     * @var null
     */
    public $stopAtHash = null;

    /**
     * @param null $rootElement
     *
     * @return array
     * @throws \Exception
     */
    public function extract($rootElement = null) {

        if($rootElement == null){
            $rootElement = $this->crawler->getPage()->find('xpath', $this->rules->extraction->resultXPaths[0]);
        }

        if($rootElement == null){
            throw new \Exception('Multiple Extractor Error : Could not select root element');
        }

        $rows = $rootElement->findAll('xpath',$this->rules->extraction->rowXPaths[0]);

        $results = array();

        foreach($rows as $row){
            $result = parent::extract($row);
            if($this->stopAtHash !=  null && $this->stopAtHash == $result['hash']){
                $this->crawler->maxPages = 1; // Forcefully break the crawling
                break;
            }
            if(!count($result)){
                continue;
            }
            $results[] = $result;
        }

        return $results;

    }
}