<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 9/13/16
 * Time: 11:27 AM
 */

namespace Sugar\ElasticSearch\Endpoint\Abstracts\POST;


class IndexMapping extends AbstractPostESEndpoint
{
    protected $_URL = '$index/$mapping';
}