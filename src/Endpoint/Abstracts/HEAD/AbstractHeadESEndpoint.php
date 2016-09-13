<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 9/13/16
 * Time: 9:45 AM
 */

namespace Sugar\ElasticSearch\Endpoint\Abstracts\HEAD;


use Sugar\ElasticSearch\Endpoint\Abstracts\AbstractESEndpoint;
use Sugar\ElasticSearch\Request\HEAD;
use SugarAPI\SDK\Response\JSON;

class AbstractHeadESEndpoint extends AbstractESEndpoint
{

    public function __construct($url, array $options = array())
    {
        $this->setRequest(new HEAD());
        //TODO: Make a HEAD Response Object, since it will never have a body
        $this->setResponse(new JSON($this->Request->getCurlObject()));
        parent::__construct($url, $options);
    }

}