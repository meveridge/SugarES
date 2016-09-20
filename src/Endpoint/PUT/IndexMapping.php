<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 9/13/16
 * Time: 11:02 AM
 */

namespace Sugar\ElasticSearch\Endpoint\PUT;


use Sugar\ElasticSearch\Endpoint\Abstracts\PUT\AbstractPutESEndpoint;

class IndexMapping extends AbstractPutESEndpoint
{
    protected $_URL = '$index/_mapping/$mapping';
}