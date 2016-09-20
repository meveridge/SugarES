<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 9/13/16
 * Time: 10:03 AM
 */

namespace Sugar\ElasticSearch\Endpoint\GET;


use Sugar\ElasticSearch\Endpoint\Abstracts\GET\AbstractGetESEndpoint;

class Index extends AbstractGetESEndpoint
{
    protected $_URL = '$index';

    protected $_SUPPORTED_VERSIONS = array(
        '1.4.4'
    );

    protected $Options = array(
        '_all'
    );

}