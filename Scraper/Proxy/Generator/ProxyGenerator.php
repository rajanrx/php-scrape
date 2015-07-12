<?php
/**
 * Created by PhpStorm.
 * User: rajan
 * Date: 22/06/15
 * Time: 12:42 PM
 */

namespace Scraper\Proxy\Generator;

/**
 * Class ProxyGenerator
 * @package Scraper\Proxy\Generator
 */
abstract class ProxyGenerator {

    /**
     * @var int Maximum time period for proxy server to send ping response
     */
    protected static $waitTimeOutInSeconds = 1;

    /**
     * Returns class name
     * @return string
     */
    public static function className(){
        return get_called_class();
    }

    /**
     * Generates Proxy
     * @return mixed
     */
    abstract public function generateProxy();

    /**
     * Checks if proxy is alive and fast enough
     * @param $ip
     * @param $port
     *
     * @return bool
     */
    public static function checkProxy($ip, $port){
        $working = false;

        $fp = null;
        if ($fp = @fsockopen($ip, $port, $errCode, $errStr, self::$waitTimeOutInSeconds)) {
            $working = true;
        }
        if ($fp) {
            @fclose($fp);
        }

        return $working;
    }

}