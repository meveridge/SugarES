<?php

namespace Sugar\ElasticSearch\Endpoint\Abstracts\PUT;

use Sugar\ElasticSearch\Endpoint\Abstracts\AbstractESEndpoint;
use SugarAPI\SDK\Request\PUT;
use SugarAPI\SDK\Response\JSON;

class AbstractPutESEndpoint extends AbstractESEndpoint
{
    public function __construct($url, array $options = array())
    {
        $this->setRequest(new PUT());
        $this->setResponse(new JSON($this->Request->getCurlObject()));
        parent::__construct($url, $options);
    }

}