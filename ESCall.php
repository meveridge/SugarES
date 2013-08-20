<?php

class ESCall {
	
	var $host = "localhost";
	var $port = "9200";
	
	var $indexName = "";
	var $type = "";
	var $queryString = "";
	var $jsonQueryString = "";
	var $fieldArray = array();
	
	var $errorArray = array();
	
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
		
		//load server data (stats)
		$this->loadServerStats();
		
	}
	
	public function getDocsByIndexAndType($inputType){
		
		$returnedCount = 50;
		
		$this->queryString = $this->host . ":" . $this->port;
		$this->queryString .= "/" . $this->indexName;
		$this->queryString .= "/" . $inputType;
		$this->queryString .= "/_search?size=$returnedCount";
		
		$docResults = $this->executeQuery();
		
		$totalCount = $docResults['hits']['total'];
		
		
		if($totalCount < $returnedCount) $returnedCount = $totalCount;
		$this->logError("success","record_count","Returned $returnedCount of $totalCount records.");
		
		$docTreeHTML = "<ul id=\"{$this->indexName}_{$inputType}_docs\" class=\"treeAction\">";
		foreach($docResults['hits']['hits'] as $key => $value){
			$docId = $value['_id'];
			if(strlen($docId)>15){
				$docIdDisplay = substr($docId,0,15) . "...";
			}else{
				$docIdDisplay = $docId;
			}
			$docTreeHTML .= "<li id=\"tree_{$docId}\" class=\"treeAction\">$docId</li>";
		}
		$docTreeHTML .= "</ul>";
		return "<div id=\"docTreeHTML\">$docTreeHTML</div>";
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
			
			ksort($value['mappings']);
			foreach($value['mappings'] as $subkey => $subvalue){
				$this->ESMetadata['indexes'][$key]['modules'][$subkey]['fields'] = $subvalue['properties'];
			}
		}
	}
	
	/* loadServerStats()
	 * Queries the Server to retrieve the server
	 * and index stats.
	 * 
	 */
	public function loadServerStats(){
		
		$this->queryString = $this->host . ":" . $this->port;
		$this->queryString .= "/_stats?indexing=false&get=false&search=false";
		
		$serverStats = $this->executeQuery();
		
		if(count($serverStats)>0){
			
			//populate server data
			$this->ESMetadata['total_docs'] = $serverStats['_all']['total']['docs']['count'];
			$this->ESMetadata['deleted_docs'] = $serverStats['_all']['total']['docs']['deleted'];
			
			$this->ESMetadata['store_size'] = $serverStats['_all']['total']['store']['size'];
			
			//populate index data
			if(isset($serverStats['_all']['indices']) && count($serverStats['_all']['indices'])>0){
				$indices = $serverStats['_all']['indices'];
				foreach($indices as $indexName => $indexStats){
				
					$this->ESMetadata['indexes'][$indexName]['index_total_docs'] = $indexStats['total']['docs']['count'];
					$this->ESMetadata['indexes'][$indexName]['index_deleted_docs'] = $indexStats['total']['docs']['deleted'];
					
					$this->ESMetadata['indexes'][$indexName]['index_store_size'] = $indexStats['total']['store']['size'];
					
				}
			}else{
				$this->logError("warning","empty_array","Function loadServerStats() returned an empty indices array.");
			}
		}else{
			$this->logError("warning","empty_array","Function loadServerStats() returned an empty array.");
		}
		
	}

	/*generateTreeHTML()
	 * Populates the tree in the actions tab
	 */
	public function generateTreeHTML(){
		
		if(count($this->ESMetadata['indexes'])>0){
			
			$treeHTML = "<ul class=\"treeAction\">";
			foreach($this->ESMetadata['indexes'] as $indexName => $metadata){
					
				if(strlen($indexName)>20){
					$indexNameDisplay = substr($indexName,0,20) . "...";
				}else{
					$indexNameDisplay = $indexName;
				}
				
				$treeHTML .= "<li onClick=\"changeActiveIndex('$indexName')\" style=\"cursor:pointer;\">";
				$treeHTML .= "<i class=\"icon-minus-sign\" data-toggle=\"collapse\" data-target=\"#tab1_$indexName\" id=\"{$indexName}_icon\" onClick=\"changeTreeIcon('{$indexName}_icon');\"></i>$indexNameDisplay";
				$treeHTML .= "<ul id=\"tab1_$indexName\" class=\"treeAction collapse in\">";
				
				foreach($metadata['modules'] as $moduleName => $fields){
					$treeHTML .= "<li id=\"{$indexName}_{$moduleName}\">";
					$treeHTML .= "<i id=\"{$indexName}_{$moduleName}_icon\" class=\"icon-plus-sign\" data-toggle=\"collapse\" data-target=\"#{$indexName}_{$moduleName}_docs\" onClick=\"retrieveDocsByIndexAndType('{$indexName}','{$moduleName}');\"></i>";
					$treeHTML .= "$moduleName<div id=\"{$indexName}_{$moduleName}_child\"></div></li>";
				}
				
				$treeHTML .= "</ul></li>";
			}
			$treeHTML .= "</ul>";
			
			return "<div id=\"treeHTML\">$treeHTML</div>";
		}else{
			$this->logError("error","empty_array","ESMetadata is Empty, please check the settings entered.");
			return "( empty )";
			
		}
		
	}

	/*generateStatsHTML(0)
	 * Populates the stats for the server and all indexes
	 */
	public function generateStatsHTML(){
		
		if(count($this->ESMetadata)>0){
			
			//server stats
			$serverStatsHTML = "<fieldset><legend>Server Stats</legend>";
			if(isset($this->ESMetadata['cluster_name'])===true){
				$serverStatsHTML .= "<div class=\"row-fluid\"><label class=\"span6\">Cluster Name: </label><label class=\"span6\">{$this->ESMetadata['cluster_name']}</label></div>";	
			}else{
				$this->logError("info","undefined_index","ESMetadata is missing the cluster_name index.");
			}
			
			if(isset($this->ESMetadata['total_docs'])===true){
				$serverStatsHTML .= "<div class=\"row-fluid\"><label class=\"span6\">Total Docs: </label><label class=\"span6\">{$this->ESMetadata['total_docs']}</label></div>";
			}else{
				$this->logError("info","undefined_index","ESMetadata is missing the total_docs index.");
			}
			
			if(isset($this->ESMetadata['deleted_docs'])===true){
				$serverStatsHTML .= "<div class=\"row-fluid\"><label class=\"span6\">Deleted Docs: </label><label class=\"span6\">{$this->ESMetadata['deleted_docs']}</label></div>";
			}else{
				$this->logError("info","undefined_index","ESMetadata is missing the deleted_docs index.");
			}
			
			if(isset($this->ESMetadata['store_size'])===true){
				$serverStatsHTML .= "<div class=\"row-fluid\"><label class=\"span6\">Store Size: </label><label class=\"span6\">{$this->ESMetadata['store_size']}</label></div>";
			}else{
				$this->logError("info","undefined_index","ESMetadata is missing the store_size index.");
			}
			
			$serverStatsHTML .= "</fieldset>";
			
			//index stats
			if(count($this->ESMetadata['indexes'])>0){
				$indexStatsHTML = "";
				$firstIndexId = "";
				foreach($this->ESMetadata['indexes'] as $indexName => $stats){
					
					if($firstIndexId==""){
						$firstIndexId = "index_stats_$indexName";
						$indexStatsHTML .= "<input type=hidden id=\"activeIndexId\" value=\"$firstIndexId\" />";
					}
					$indexStatsHTML .= "<fieldset style=\"display:none;\" id=\"index_stats_$indexName\"><legend>Index Stats</legend>";
					$indexStatsHTML .= "<div class=\"row-fluid\"><label class=\"span6\">Index Name: </label><label class=\"span6\">$indexName</label></div>";
					
					if(isset($stats['index_total_docs'])===true){
						$indexStatsHTML .= "<div class=\"row-fluid\"><label class=\"span6\">Total Docs: </label><label class=\"span6\">{$stats['index_total_docs']}</label></div>";
					}else{
						$this->logError("info","undefined_index","Stats Result is missing the index_total_docs index.");
					}
					
					if(isset($stats['index_deleted_docs'])===true){
						$indexStatsHTML .= "<div class=\"row-fluid\"><label class=\"span6\">Deleted Docs: </label><label class=\"span6\">{$stats['index_deleted_docs']}</label></div>";
					}else{
						$this->logError("info","undefined_index","Stats Result is missing the index_deleted_docs index.");
					}
					
					if(isset($stats['index_store_size'])===true){
						$indexStatsHTML .= "<div class=\"row-fluid\"><label class=\"span6\">Store Size: </label><label class=\"span6\">{$stats['index_store_size']}</label></div>";
					}else{
						$this->logError("info","undefined_index","Stats Result is missing the index_store_size index.");
					}
					
					if(isset($stats['state'])===true){
						$indexStatsHTML .= "<div class=\"row-fluid\"><label class=\"span6\">Index State: </label><label class=\"span6\">{$stats['state']}</label></div>";
					}else{
						$this->logError("info","undefined_index","Stats Result is missing the state index.");
					}
					if(isset($stats['version'])===true){
						$indexStatsHTML .= "<div class=\"row-fluid\"><label class=\"span6\">Index Version: </label><label class=\"span6\">{$stats['version']}</label></div>";
					}else{
						$this->logError("info","undefined_index","Stats Result is missing the version index.");
					}
					
					$indexStatsHTML .= "</fieldset>";
					
				}
			}else{
				$this->logError("error","empty_array","ESMetadata['indexes'] is Empty.");
			}
			
			return "<div id=\"serverStatsHTML\">$serverStatsHTML</div><br><div id=\"indexStatsHTML\">$indexStatsHTML</div>";
		}else{
			$this->logError("error","empty_array","ESMetadata is Empty, please check the settings entered.");
			return "( empty )";
			
		}
		
	}

	public function logError($severity,$type,$description){
		$errorArray = array(
			'severity' => $severity,
			'type' => $type,
			'description' => $description,
		);	
		$this->errorArray[] = $errorArray;
	}

	public function populateErrorHTML(){
		
		if(count($this->errorArray)>0){
			$errorHTML = "<fieldset><legend>Log</legend>";	
			foreach($this->errorArray as $errorId => $errorContent){
				$errorHTML .= "<div class=\"row-fluid text-{$errorContent['severity']}\">";
				$errorHTML .= "<label class=\"span2\">{$errorContent['type']}</label>";
				$errorHTML .= "<div class=\"span10\">{$errorContent['description']}</div></div>";
			}
			$errorHTML .= "</fieldset>";
			return "<div id=\"errorHTML\">$errorHTML</div>";
		}		
	}
	
	public function executeQuery($method = "XGET"){
		//echo"execute executeQuery:$method:url={".$this->queryString."}<br>";
		// create a new cURL resource
		$ch = curl_init();
		$this->logError("info","curl_url",$this->queryString);
		
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
		
		$curlError = curl_error($ch);
		if(!empty($curlError)){
			$this->logError("error","curl_error",$curlError);
		}
		
		//if($results){echo"Results = TRue";}
		
		// close cURL resource, and free up system resources
		curl_close($ch);
		
		$result_array = json_decode($results,TRUE);
		if(count($result_array)>0){
			if(isset($result_array['exists']) && $result_array['exists']==false){
				$this->logError("error","es_error","Elasticsearch did not return a proper dataset.");
			}
		}else{
			$this->logError("error","es_error","Elasticsearch did not return a proper dataset.");
		}
		
		return $result_array;	
	}
	
	
	
//everything below here is to  be deleted....

	
	
	
	
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