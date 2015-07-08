<?php
/**
 * Created by PhpStorm.
 * User: rajan
 * Date: 25/06/15
 * Time: 12:48 PM
 */

namespace scraper\scrape\extractor\types;


use scraper\scrape\extractor\BaseExtractor;

/**
 * Class SingleRowExtractor
 * @package scraper\scrape\extractor\types
 */
class SingleRowExtractor extends BaseExtractor {


    /**
     * @param null $rootElement
     *
     * @return array
     * @throws \Exception
     */
    public function extract($rootElement = null) {

        $fields = array();
        $resultPipeLine = $this->rules->extraction->resultPipeline;

        if ($rootElement == null) {
            $rootElement = $this->crawler->getPage()->find('xpath', $this->rules->extraction->resultXPaths[0]);
        }

        if ($rootElement == null) {
            throw new \Exception('Single Extractor Error : Could not select root element');
        }

        foreach ($resultPipeLine as $pipeline) {

            if (isset($pipeline->configuration->xpaths)) {
                $element = $rootElement->find('xpath', $pipeline->configuration->xpaths[0]);
                if ($element != null) {

                    $configuration = $pipeline->configuration;
                    $fields[$configuration->property] = $element->getText();

                    if (isset($configuration->type) && $configuration->type == 'HTML') {
                        $fields[$configuration->property] = $element->getOuterHtml();
                    }

                    if(isset($configuration->regexp)){
                        $fields[$configuration->property] = $this->parseRegex($fields[$configuration->property],$configuration->regexp);
                    }

                    if (isset($configuration->type) && $configuration->type == 'DATE') {
                        $fields[$configuration->property] = date("Y-m-d h:i:s",strtotime($fields[$configuration->property]));
                    }
                }
            }
        }

        if (count($fields)) {
            $fields['hash'] = md5(json_encode($fields));
        }

        return $fields;

    }

    /**
     * @param $string
     * @param $regex
     *
     * @return array|null
     */
    private function parseRegex($string, $regex){

        preg_match_all($regex,$string,$matches,PREG_SET_ORDER);

        if(!count($matches)){
            return null;
        }

        if(count($matches) == 1){
            return $matches[0][1];
        }

        $results = array();
        foreach($matches as $match){
            $results[] = $match[1];
        }

        return $results;

    }
}