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
 * @package Scraper\Proxy\Structure
 */
interface ProxyInterface {

    /**
     * Returns Proxy structure
     * @return Proxy
     */
    public function get();

    /**
     * Returns proxy url
     * @return mixed
     */
    public function getUrl();

}