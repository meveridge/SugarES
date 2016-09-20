<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 9/13/16
 * Time: 10:58 AM
 */

namespace Sugar\ElasticSearch\Endpoint\GET;


use Sugar\ElasticSearch\Endpoint\Abstracts\GET\AbstractGetESEndpoint;

class IndexMapping extends AbstractGetESEndpoint
{
    protected $_URL = '$index/_mapping/$mapping';

}