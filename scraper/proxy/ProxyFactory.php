<?php
/**
 * Created by PhpStorm.
 * User: rajan
 * Date: 22/06/15
 * Time: 12:38 PM
 */

namespace Scraper\Proxy;
use Scraper\Proxy\Generator\ProxyGenerator;
use Scraper\Proxy\Structure\ProxyInterface;

/**
 * Class ProxyFactory
 * @package scraper\proxy
 */
class ProxyFactory {
    /**
     * @var ProxyFactory reference to singleton instance
     */
    private static $instance;

    /**
     * gets the instance via lazy initialization (created on first usage)
     *
     * @return ProxyFactory
     */
    public static function getInstance() {
        if (null === static::$instance) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * @param ProxyGenerator $generator
     *
     * @return ProxyInterface[]
     * @throws \Exception
     */
    public function getProxy(ProxyGenerator $generator){

        if(!is_a($generator,ProxyGenerator::className())){
            throw new \Exception("Provided generator is not of type ProxyGenerator");
        }

        return $generator->generateProxy();
    }

    /**
     * is not allowed to call from outside: private!
     *
     */
    private function __construct() {
    }

    /**
     * prevent the instance from being cloned
     *
     * @return void
     */
    private function __clone() {
    }

    /**
     * prevent from being un-serialized
     *
     * @return void
     */
    private function __wakeup() {
    }

}