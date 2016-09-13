<?php

namespace Sugar\ElasticSearch\Endpoint\Abstracts\GET;

use Sugar\ElasticSearch\Endpoint\Abstracts\AbstractESEndpoint;
use SugarAPI\SDK\Request\GET;
use SugarAPI\SDK\Response\JSON;

class AbstractGetESEndpoint extends AbstractESEndpoint
{

    public function __construct($url, array $options = array())
    {
        $this->setRequest(new GET());
        $this->setResponse(new JSON($this->Request->getCurlObject()));
        parent::__construct($url, $options);
    }

}