<?php
/**
 * ©[2016] SugarCRM Inc.  Licensed by SugarCRM under the Apache 2.0 license.
 */
// @codeCoverageIgnoreStart

$endPoints = array(
    //Dynamic Endpoints
    'indices' => 'Indices',

    //GET API Endpoints
    'index' => 'GET\\Index',
    'indexMapping' => 'GET\\IndexMapping',
    'ping' => 'GET\\Root',

    //HEAD API Endpoints
    'indexExists' => 'HEAD\\Index',

    //POST API Endpoints
    'createDocument' => 'POST\\IndexMapping',

    //PUT API Endpoints
    'putMapping' => 'PUT\\IndexMapping',
    'createIndex' => 'PUT\\Index',
    'updateDocument' => 'PUT\\IndexMappingDocument',

    //DELETE API Endpoints
    'deleteIndex' => 'DELETE\\Index',
);

// @codeCoverageIgnoreEnd
