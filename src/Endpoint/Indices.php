<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 9/12/16
 * Time: 4:41 PM
 */

namespace Sugar\ElasticSearch\Endpoint;

use Sugar\ElasticSearch\Endpoint\DELETE\Index as DeleteIndex;
use Sugar\ElasticSearch\Endpoint\GET\Index as GetIndex;
use Sugar\ElasticSearch\Endpoint\GET\IndexMapping;
use Sugar\ElasticSearch\Endpoint\HEAD\Index as IndexExists;
use Sugar\ElasticSearch\Endpoint\PUT\Index as CreateIndex;
use SugarAPI\SDK\Request\GET;

class Indices extends GetIndex {


    protected $_URL = '$index';

    public function setOptions(array $options){
        if (count($options)>1){
            $indices = implode(",",$options);
            $options = array($indices);
        }
        return parent::setOptions($options);
    }

    public function execute($data = null){
        if ($this->supported($this->client_version)){
            return parent::execute($data);
        }else{
            //TODO - What is the best method for Gettings an Index pre 1.4.x?
            $GetIndex = new IndexMapping($this->baseUrl,$this->Options);
            return $GetIndex->execute($data);
        }
    }

    public function exists(){
        $Exists = new IndexExists($this->baseUrl,$this->Options);
        $Exists->setVersion($this->client_version);
        $response = $Exists->execute()->getResponse();
        $Exists->getRequest()->close();
        if ($response->getStatus() == '200'){
            unset($Exists);
            return TRUE;
        }else{
            unset($Exists);
            return FALSE;
        }
    }

    public function create($data = NULL){
        $Endpoint = new CreateIndex($this->baseUrl,$this->Options);
        $Endpoint->setVersion($this->client_version);
        return $Endpoint->execute($data)->getResponse();
    }

    public function delete(){
        $Endpoint = new DeleteIndex($this->baseUrl,$this->Options);
        $Endpoint->setVersion($this->client_version);
        return $Endpoint->execute()->getResponse();
    }

}