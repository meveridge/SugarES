<?php

namespace Sugar\ElasticSearch\Client\Abstracts;


use Sugar\ElasticSearch\Helpers\Helpers;
use SugarAPI\SDK\Client\Abstracts\AbstractClient;

class AbstractESClient extends AbstractClient {

    protected $port;

    protected $version;

    protected $connected = FALSE;

    public function __construct($server = '',$port = '') {
        $server = (!empty($server)?$server:$this->server);
        $port = (!empty($port)?$server:$this->port);

        $this->registerESEndpoints();

        $this->setPort($port);
        $this->setServer($server);
    }

    protected function registerESEndpoints(){
        foreach(Helpers::registeredEndpoints() as $func => $endpoint){
            $this->registerEndpoint($func,$endpoint);
        }
    }

    public function setPort($port){
        $port = intval($port);
        $this->port = $port;
    }

    protected function setAPIUrl() {
        $apiURL = $this->apiURL;
        $this->apiURL = Helpers::configureAPIURL($this->server,$this->port);
        if ($this->apiURL !== $apiURL){
            $this->connect();
        }
    }

    public function __call($name, $params) {
        $EndPoint = parent::__call($name, $params);
        if (!empty($this->version)) {
            $EndPoint->setVersion($this->version);
        }
    }

    public function connect(){
        if (!(empty($this->apiURL))){
            $Ping = $this->ping()->execute();
            $response = $Ping->getResponse();
            if ($response->getStatus() == '200'){
                $Ping->getRequest()->close();
                $this->connected = TRUE;
                $this->version = $response['version']['number'];
                return TRUE;
            }
            unset($Ping);
        }
        return FALSE;

    }

    public function getVersion(){
        return $this->version;
    }

    public function isSupported(){
        if (empty($this->version)){
            return TRUE;
        }
        return Helpers::sugarSupported($this->version);
    }

    /**
     * Return True, since Authentication is not part of ElasticSearch
     * @return bool
     */
    public function login(){
        return TRUE;
    }

    /**
     * Return True, since Authentication is not part of ElasticSearch
     * @return bool
     */
    public function refreshToken() {
        return TRUE;
    }

}