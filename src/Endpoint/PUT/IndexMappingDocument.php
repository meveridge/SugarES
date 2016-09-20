<?php

namespace Sugar\ElasticSearch\Endpoint\PUT;


use Sugar\ElasticSearch\Endpoint\Abstracts\PUT\AbstractPutESEndpoint;

class IndexMappingDocument extends AbstractPutESEndpoint
{
    protected $_URL = '$index/$mapping/$document';
}