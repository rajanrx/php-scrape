<?php
/**
 * Created by PhpStorm.
 * User: rajan
 * Date: 22/06/15
 * Time: 12:50 PM
 */

namespace Scraper\Proxy\Structure;


/**
 * Interface ProxyInterface
 * @package app\models\proxy
 */
interface ProxyInterface {

    /**
     * @return Object
     */
    public function get();

    public function getUrl();

}