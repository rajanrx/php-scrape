<?php
/**
 * Created by PhpStorm.
 * User: rajan
 * Date: 22/06/15
 * Time: 12:59 PM
 */

namespace Scraper\Proxy\Structure;


/**
 * Class Proxy
 * @package Scraper\Proxy\Structure
 */
class Proxy  implements ProxyInterface {

    /**
     * @var String Ip address
     */
    public $ip;

    /**
     * @var Integer Port for connection
     */
    public $port;

    /**
     * @var String Speed of connection
     */
    public $speed;

    /**
     * @var array Types of allowed connections
     */
    public $types = array();

    /**
     * @var String Default connection type
     */
    public $type;

    /**
     * @var bool Working status
     */
    public $working;

    /**
     * @var String Country of the proxy server
     */
    public $country;

    /**
     * @var String level of anonymity
     */
    public $anonymity;

    /**
     * {@inheritdoc}
     * @return $this
     */
    public function get() {
        return $this;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getUrl(){
        if($this->type == null){
            return $this->ip.':'.$this->port;
        }
        return strtolower($this->type).'://'.$this->ip.':'.$this->port;
    }
}