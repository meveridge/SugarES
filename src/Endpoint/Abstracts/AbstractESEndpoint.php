<?php

namespace Sugar\ElasticSearch\Endpoint\Abstracts;

use \SugarAPI\SDK\Endpoint\Abstracts\AbstractEndpoint;

abstract class AbstractESEndpoint extends AbstractEndpoint
{
    protected $_AUTH_REQUIRED = FALSE;

    protected $_SUPPORTED_VERSIONS = array();

    protected $client_version;

    public function setVersion($version){
        $this->client_version = $version;
    }

    public function supported($version){
        if (empty($this->_SUPPORTED_VERSIONS)){
            return TRUE;
        }else{
            return in_array($version,$this->_SUPPORTED_VERSIONS);
        }
    }
}