<?php

namespace Sugar\ElasticSearch\Endpoint\Abstracts\DELETE;


use Sugar\ElasticSearch\Endpoint\Abstracts\AbstractESEndpoint;
use SugarAPI\SDK\Request\DELETE;
use SugarAPI\SDK\Response\JSON;

class AbstractDeleteESEndpoint extends AbstractESEndpoint
{

    public function __construct($url, array $options = array())
    {
        $this->setRequest(new DELETE());
        $this->setResponse(new JSON($this->Request->getCurlObject()));
        parent::__construct($url, $options);
    }

}