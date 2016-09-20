<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 9/12/16
 * Time: 4:01 PM
 */

namespace Sugar\ElasticSearch\Helpers;


class Helpers {

    const DEFAULT_PORT = 9200;

    protected static $_supported_versions = array(
        '0.19.3',
        '0.90.10',
        '1.3.1',
        '1.4.4'
    );

    public static function registeredEndpoints(){
        $endPoints = array();
        require __DIR__.DIRECTORY_SEPARATOR.'registry.php';
        foreach ($endPoints as $funcName => $className) {
            $className = "Sugar\\ElasticSearch\\Endpoint\\" . $className;
            $entryPoints[$funcName] = $className;
        }
        return $endPoints;
    }

    public static function sugarSupported($version){
        return in_array($version,static::$_supported_versions);
    }

    public static function configureAPIURL($server,$port = NULL){
        $url = '';
        $server = strtolower(rtrim($server, "/"));
        $port = ($port === null ? self::DEFAULT_PORT : intval($port));
        if (preg_match('/^(http|https):\/\//i', $server) === 0) {
            $server = "http://".$server;
        }
        $server = preg_replace('/:[0-9]+$/', '', $server);
        $url = $server.':'.$port;
        return $url;

    }

}