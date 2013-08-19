<?php

class ESCall {
	
	var $host = "localhost";
	var $port = "9200";
	
	var $indexName = "";
	var $type = "";
	var $queryString = "";
	var $jsonQueryString = "";
	var $fieldArray = array();
	
	var $serverStats = array();
	var $ESMetadata = array();
	
	
	
	public function __construct($hostOverride="",$portOverride="",$indexOverride=""){
		//echo"execute __construct<br>";
		if($hostOverride != "") $this->host = $hostOverride;
		if($portOverride != "") $this->port = $portOverride;
		if($indexOverride != "") $this->indexName = $indexOverride;
		
	}
	
	public function connect(){
			
		//load es metadata
		$this->loadESMetadata();
		
		//load server data (stats and settings)
		//loadServerData();
		
		//load index stats
		
		

	}
	
	/* loadESMetadata()
	 * Queries the Server to retrieve the indexes, the version of the index,
	 * and the Mappings of the index.
	 * Mappings include Module Names and Fields in the index.
	 * 
	 */
	public function loadESMetadata(){
		//http://localhost:9201/_cluster/state?filter_nodes=true&filter_routing_table=true&filter_blocks=true
		//http://localhost:9201/_cluster/state?filter_nodes=true&filter_routing_table=true&filter_blocks=true
		
		$this->queryString = $this->host . ":" . $this->port;
		$this->queryString .= "/_cluster/state?filter_nodes=true&filter_routing_table=true&filter_blocks=true";
		
		$ESMetadata = $this->executeQuery();
		
		$this->ESMetadata['cluster_name'] = $ESMetadata['cluster_name'];
		
		$indices = $ESMetadata['metadata']['indices'];
		
		foreach($indices as $key => $value){
		
			$this->ESMetadata['indexes'][$key] = array(
				"state" => $value['state'],
				"version" => "0.".$value['settings']['index.version.created'],
			);
			
			foreach($value['mappings'] as $subkey => $subvalue){
				$this->ESMetadata['indexes'][$key]['modules'][$subkey]['fields'] = $subvalue['properties'];
			}
			
		}
		/*
		echo"Start Results:<br><pre>";
		print_r($this->ESMetadata);
		echo"</pre>";
		*/
		
		
	}

	/*populateTree(0)
	 * Populates the tree in the actions tab
	 */
	public function generateTreeHTML(){
		$treeHTML = "<ul class=\"treeAction\">";

		foreach($this->ESMetadata['indexes'] as $indexName => $metadata){
			$treeHTML .= "<li><i class=\"icon-minus-sign\" data-toggle=\"collapse\" data-target=\"#tab1_$indexName\"></i>".substr($indexName,0,20)."...";
			$treeHTML .= "<ul id=\"tab1_$indexName\" class=\"treeAction collapse in\">";
			
			foreach($metadata['modules'] as $moduleName => $fields){
				$treeHTML .= "<li><i class=\"icon-plus-sign\"></i>$moduleName</li>";
			}
			
			$treeHTML .= "</ul></li>";
		}
		$treeHTML .= "</ul>";
		
		return $treeHTML;
		
	}
	
	public function loadServerStats(){
		
		$this->queryString = $this->host . ":" . $this->port;
		$this->queryString .= "_stats";
		
		$this->serverStats = $this->executeQuery();
	}
	
	public function executeQuery($method = "XGET"){
		echo"execute executeQuery:$method:url={".$this->queryString."}<br>";
		// create a new cURL resource
		$ch = curl_init();
		
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $this->queryString);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		if($method=="XPOST"){
			//curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');	
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->jsonQueryString);
		}elseif($method=="XPUT"){
			//curl_setopt($ch, CURLOPT_PUT, TRUE);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
			//curl_setopt($ch, CURLOPT_POSTFIELDS, $this->jsonQueryString);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		
		//
		// grab URL and pass it to the browser
		$results = curl_exec($ch);
		echo"CURL Error:".curl_error($ch);
		
		if($results){echo"Results = TRue";}
		
		// close cURL resource, and free up system resources
		curl_close($ch);
		
		$result_array = json_decode($results,TRUE);
		
		if(count($result_array)>0){echo"Results count is good: ".count($result_array);}
		return $result_array;	
	}
	
	
	
	
	
	
	
	
	public function addFilter(){
		
	}
	
	public function buildFilterOptions($indexNameOverride = "",$typeOverride = ""){
		echo"execute buildFilterOptions<br>";
		if($indexNameOverride != "") $this->indexName = $indexNameOverride;
		if($typeOverride != "") $this->type = $typeOverride;
		
		$this->queryString = $this->host . ":" . $this->port;
		$this->queryString .= ($indexNameOverride==""?"":"/".$this->indexName);
		$this->queryString .= ($typeOverride==""?"":"/".$this->type);
		$this->queryString .= "/_mapping";
		
		$filterOptions = $this->executeQuery();
		return $filterOptions;
	}
	
	public function buildQueryString(){
		echo"execute buildQueryString<br>";
		
	}
	
	public function buildInsertString($indexNameOverride = "", $typeOverride = "", $docId = ""){
		if($indexNameOverride != "") $this->indexName = $indexNameOverride;
		if($typeOverride != "") $this->type = $typeOverride;
		
		$this->queryString = $this->host . ":" . $this->port;
		$this->queryString .= ($indexNameOverride==""?"":"/".$this->indexName);
		$this->queryString .= ($typeOverride==""?"":"/".$this->type);
		$this->queryString .= ($docId==""?"/".uniqid():"/".$docId);
		
		$this->jsonQueryString = json_encode($this->fieldArray);
		
	}
	
	public function addFieldToUpdateString($field,$value){
		$this->fieldArray[$field] = $value;
	}
	

}

?>