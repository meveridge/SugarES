<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 9/12/16
 * Time: 3:55 PM
 */

namespace Sugar\ElasticSearch\Request;

use SugarAPI\SDK\Request\GET;

class HEAD extends GET
{
    /**
     * @inheritdoc
     */
    protected static $_TYPE = 'HEAD';

}