<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 9/13/16
 * Time: 10:42 AM
 */

namespace Sugar\ElasticSearch\Endpoint\HEAD;


use Sugar\ElasticSearch\Endpoint\Abstracts\HEAD\AbstractHeadESEndpoint;

class Index extends AbstractHeadESEndpoint
{
    protected $_URL = '$index';

}