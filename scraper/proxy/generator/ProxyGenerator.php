<?php
/**
 * Created by PhpStorm.
 * User: rajan
 * Date: 22/06/15
 * Time: 12:42 PM
 */

namespace scraper\proxy\generator;

abstract class ProxyGenerator {

    protected static $waitTimeOutInSeconds = 1;

    public static function className(){
        return get_called_class();
    }

    abstract public function generateProxy();

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