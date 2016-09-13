<?php

namespace Sugar\ElasticSearch\Endpoint\DELETE;

use Sugar\ElasticSearch\Endpoint\Abstracts\DELETE\AbstractDeleteESEndpoint;

class Index extends AbstractDeleteESEndpoint
{
    protected $_URL = '$index';

}