<?php

namespace Sugar\ElasticSearch\Endpoint\Abstracts\POST;

use Sugar\ElasticSearch\Endpoint\Abstracts\AbstractESEndpoint;
use SugarAPI\SDK\Request\POST;
use SugarAPI\SDK\Response\JSON;

class AbstractPostESEndpoint extends AbstractESEndpoint
{
    public function __construct($url, array $options = array())
    {
        $this->setRequest(new POST());
        $this->setResponse(new JSON($this->Request->getCurlObject()));
        parent::__construct($url, $options);
    }

}