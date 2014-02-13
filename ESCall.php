<?php

class ESCall {
	
	var $host = "localhost";
	var $port = "9200";
	
	var $indexName = "";
	var $type = "";
	var $queryString = "";
	var $jsonQueryString = "";
	var $fieldArray = array();
	var $connected = false;
	
	var $errorArray = array();
	var $ESMetadata = array();

	var $searchResultCount = "50";
	var $treeResultCount = "10";
	
	public function __construct($hostOverride="",$portOverride="",$indexOverride="",$searchResultCount="",$treeResultCount=""){

		if($searchResultCount != "") $this->searchResultCount = $searchResultCount;
		if($treeResultCount != "") $this->treeResultCount = $treeResultCount;

		if($hostOverride != "") $this->host = $hostOverride;
		if($portOverride != "") $this->port = $portOverride;
		if($indexOverride != "") $this->indexName = $indexOverride;

		//Check if we have a connection
		$this->queryString = $this->host . ":" . $this->port;
		
		$connection = $this->executeQuery();

		if(is_array($connection) && isset($connection['status']) && $connection['status']=="200"){
			$this->logError("success","information","Connecting to: {$hostOverride}:{$portOverride}/{$indexOverride}");
			$this->queryString = "";
			$this->connected = true;
		}else{
			$this->logError("error","Error","Failed to connect to: {$hostOverride}:{$portOverride}/");
			return false;
		}
		
	}
	
	public function connect(){
		
		if($this->connected){
			//load es metadata
			$this->loadESMetadata();
		
			//load server data (stats)
			$this->loadServerStats();
		}
	}
	
	public function getDocsByIndexAndType($inputType){
		
		$returnedCount = $this->treeResultCount;
		
		$this->queryString = $this->host . ":" . $this->port;
		$this->queryString .= "/" . $this->indexName;
		$this->queryString .= "/" . $inputType;
		$this->queryString .= "/_search?size=$returnedCount";
		
		$docResults = $this->executeQuery();
		
		$totalCount = $docResults['hits']['total'];
		
		
		if($totalCount < $returnedCount) $returnedCount = $totalCount;
		$this->logError("success","record_count","Returned $returnedCount of $totalCount records.");
		if($totalCount > $returnedCount) $docResults['hits']['hits'][] = array("_id" => "more...");
		
		$docTreeHTML = "<ul id=\"{$this->indexName}_{$inputType}_docs\" class=\"treeAction\">";
		foreach($docResults['hits']['hits'] as $key => $value){
			$docId = $value['_id'];
			if(strlen($docId)>15){
				$docIdDisplay = substr($docId,0,15) . "...";
			}else{
				$docIdDisplay = $docId;
			}
			if($docIdDisplay == "more..."){
				$docTreeHTML .= "<li id=\"{$this->indexName}_{$inputType}_tree_{$docId}\" class=\"treeAction\" onClick=\"retrieveMoreDocsByType('{$inputType}');\">$docIdDisplay<i id=\"{$this->indexName}_{$inputType}_tree_{$docId}_icon\" class=\"pull-right\"></i></li>";
			}else{
				$docTreeHTML .= "<li id=\"{$this->indexName}_{$inputType}_tree_{$docId}\" class=\"treeAction\" onClick=\"retrieveDocById('{$this->indexName}','{$inputType}','{$docId}');\">$docIdDisplay<i id=\"{$this->indexName}_{$inputType}_tree_{$docId}_icon\" class=\"pull-right\"></i></li>";
			}
		}
		$docTreeHTML .= "</ul>";
		return "<div id=\"docTreeHTML\">$docTreeHTML</div>";
	}
	
	public function getDocById($inputType,$inputId){
		
		$this->queryString = $this->host . ":" . $this->port;
		$this->queryString .= "/" . $this->indexName;
		$this->queryString .= "/" . $inputType;
		$this->queryString .= "/" . $inputId;
		
		$docResults = $this->executeQuery();
		
		$_index = $docResults['_index'];
		$_type = $docResults['_type'];
		$_id = $docResults['_id'];
		$_version = $docResults['_version'];
		
		$docHTML = "<fieldset><legend>Record Results</legend>";
		$docHTML .= "<table class=\"table table-striped table-condensed table-bordered\">";
		$docHTML .= "<tr><td>Index Name</td><td>$_index</td></tr>";
		$docHTML .= "<tr><td>Type</td><td>$_type</td></tr>";
		$docHTML .= "<tr><td>Id</td><td>$_id</td></tr>";
		$docHTML .= "<tr><td>Doc Version</td><td>$_version</td></tr>";
		$docHTML .= "</table><div class=\"row-fluid\"><dl class=\"dl-horizontal\">";
		
		foreach($docResults['_source'] as $fieldName => $fieldValue){
			if(!isset($fieldValue)||$fieldValue=="") $fieldValue = "&nbsp;";
			$docHTML .= "<dt>$fieldName:</dt><dd>$fieldValue</dd>";
		}
		
		$docHTML .= "</dl></div></fieldset>";

		return "<div id=\"docHTML\">$docHTML</div>";
	}
	
	public function getDocsByQuery($inputType,$queryId,$queryString){
		
		$returnedCount = $this->searchResultCount;
		
		$this->queryString = $this->host . ":" . $this->port;
		$this->queryString .= "/" . $this->indexName;
		$this->queryString .= "/" . $inputType;
		if($queryId != ""){
			$this->queryString .= "/" . $queryId;
		}elseif($queryString == ""){
			$this->queryString .= "/_search?size=$returnedCount";
		}else{
			$queryString = urlencode($queryString);
			$this->queryString .= "/_search?size=$returnedCount&q=$queryString";
		}

		$docResults = $this->executeQuery();
		
		$totalCount = $docResults['hits']['total'];
		
		if($totalCount == "" && $docResults['exists'] == "true"){
			//means we searched on an id, not a query... or there was an error.
			$totalCount = 1;
			$resultArray = array($docResults);
		}else{
			$resultArray = $docResults['hits']['hits'];
		}

		if($totalCount < $returnedCount) $returnedCount = $totalCount;
		$this->logError("success","record_count","Returned $returnedCount of $totalCount records.");
		
		$docHTML = "<fieldset><legend>Record Results</legend>";
		if($totalCount>0){
			$docHTML .= "<table class=\"table table-striped table-condensed table-bordered\">";
			$docHTML .= "<tr><th>Type</th><th>Id</th><th>Score</th><th>Data</th></tr>";
			
			foreach($resultArray as $rowNum => $data){
				$docHTML .= "<tr><td>{$data['_type']}</td><td><a href=\"#\" onClick=\"retrieveDocById('{$this->indexName}','{$data['_type']}','{$data['_id']}');\">{$data['_id']}</a></td><td>{$data['_score']}</td>";
				$recordHTML = "<dl>";
				foreach($data['_source'] as $fieldName => $fieldValue){
					$fieldValue = htmlspecialchars($fieldValue);
					$recordHTML .= "<dt>$fieldName</dt><dd>$fieldValue</dd>";
				}
				$recordHTML .= "</dl>";
				
				$docHTML .= "
					<td>
						<a href=\"#\" class=\"btn popovercls\" data-toggle=\"tooltip\" data-placement=\"left\" data-content=\"$recordHTML\" data-original-title=\"$recordHTML\">
							<i class=\"icon-info-sign\"></i>
						</a>
					</td>
				";
				$docHTML .= "</tr>";
			}
			
			$docHTML .= "</table>";
			
			$docHTML .= "<script type=\"text/javascript\">";
			$docHTML .= "$(\"a.popovercls\").tooltip({ html : true });";
			$docHTML .= "</script>";
		}else{
			$docHTML .= "No results.";
		}

		return "<div id=\"docResultsHTML\">$docHTML</div>";
	}
	
	public function injectDoc($inputType,$inputID,$fieldsJSON){
		
		if($inputID==""){
			$queryId = uniqid();
		}else{
			$queryId = $inputID;
		}

		$this->queryString = $this->host . ":" . $this->port;
		$this->queryString .= "/" . $this->indexName;
		$this->queryString .= "/" . $inputType;
		$this->queryString .= "/" . $queryId;
	
		$this->jsonQueryString = $fieldsJSON;
		
		$docResults = $this->executeQuery("XPUT");
		
		$docHTML = print_r($docResults,true);

		$injectResult = $docResults['ok'];
		$injectId = $docResults['_id'];
		$injectType = $docResults['_type'];
		
		if($injectResult=="1"){
			$this->logError("success","inject","true");
		
			$docHTML = "<fieldset><legend>Inject Results</legend>";
		
			$docHTML .= "<table class=\"table table-striped table-condensed table-bordered\">";
			$docHTML .= "<tr><th>Type</th><th>Id</th></tr>";
			$docHTML .= "<tr><td>{$injectType}</td><td><a href=\"#\" onClick=\"retrieveDocById('{$this->indexName}','{$injectType}','{$injectId}');\">{$injectId}</a></td>";
			$docHTML .= "</tr>";
			$docHTML .= "</table>";
		}else{
			$docHTML .= "Inject Failed";
		}

		return "<div id=\"docResultsHTML\">$docHTML</div>";
	}

	/* loadESMetadata()
	 * Queries the Server to retrieve the indexes, the version of the index,
	 * and the Mappings of the index.
	 * Mappings include Module Names and Fields in the index.
	 * 
	 */
	public function loadESMetadata(){
		//http://localhost:9201/_cluster/state?filter_nodes=true&filter_routing_table=true&filter_blocks=true
		
		$this->queryString = $this->host . ":" . $this->port;
		$this->queryString .= "/_cluster/state?filter_nodes=true&filter_routing_table=true&filter_blocks=true";
		
		$ESMetadata = $this->executeQuery();
		
		$this->ESMetadata['cluster_name'] = $ESMetadata['cluster_name'];
		
		$indexOverride = $this->indexName;
		
		$indices = $ESMetadata['metadata']['indices'];
		
		foreach($indices as $key => $value){
			if($indexOverride != "" && $indexOverride != $key) continue;
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
		$indexOverride = $this->indexName;
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
					if($indexOverride != "" && $indexOverride != $indexName) continue;
					
					$this->ESMetadata['indexes'][$indexName]['index_total_docs'] = $indexStats['total']['docs']['count'];
					$this->ESMetadata['indexes'][$indexName]['index_deleted_docs'] = $indexStats['total']['docs']['deleted'];
					
					$this->ESMetadata['indexes'][$indexName]['index_store_size'] = $indexStats['total']['store']['size'];
					
				}
			}elseif(isset($serverStats['indices']) && count($serverStats['indices'])>0){
				$indices = $serverStats['indices'];
				foreach($indices as $indexName => $indexStats){
					if($indexOverride != "" && $indexOverride != $indexName) continue;
					
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
				$treeHTML .= "<i class=\"icon-minus-sign\" data-toggle=\"collapse\" data-target=\"#tab1_$indexName\" id=\"{$indexName}_icon\" onClick=\"changeTreeIcon('{$indexName}','');\"></i>$indexNameDisplay";
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

	public function generateSearchHTML($indexOverride = ""){
		if(count($this->ESMetadata['indexes'])>0){
			
			$searchHTML = "<form action='index.php' class=\"form-inline\" id=\"search\">";
			$searchHTML .= "<div class=\"row-fluid\"><label class=\"span4\" for=\"inputIndexSelect_search\">Index</label>";
			$searchHTML .= "<select class=\"span8\" name=\"inputIndexSelect_search\" id=\"inputIndexSelect_search\">";
			
			$moduleOptions = "";
			$typesAccrossAllIndexes = array();
			foreach($this->ESMetadata['indexes'] as $indexName => $metadata){
				$searchHTML .= "<option value=\"$indexName\">$indexName</option>";
				if($indexOverride != "" && $indexOverride != $indexName) continue;
				foreach($metadata['modules'] as $moduleName => $fields){
					$typesAccrossAllIndexes[] = $moduleName;
				}
			}

			$sortedTypes = array_unique($typesAccrossAllIndexes);
			sort($sortedTypes);

			foreach($sortedTypes as $key => $moduleName){
				$moduleOptions .= "<option value=\"$moduleName\">$moduleName</option>";	
			}

			$searchHTML .= "</select></div><div class=\"row-fluid\"><label class=\"span4\" for=\"inputTypeSelect_search\">Type</label>";
			$searchHTML .= "<select class=\"span8\" name=\"inputTypeSelect_search\" id=\"inputTypeSelect_search\"><option value=\"*\">Any</option>";
			$searchHTML .= $moduleOptions;
			$searchHTML .= "</select></div><div class=\"row-fluid\">";
			
			$searchHTML .= "<label class=\"span4\" for=\"inputIdQuery_search\">Id</label>";
			$searchHTML .= "<input class=\"span8\" type=\"text\" id=\"inputIdQuery_search\" name=\"inputIdQuery_search\" placeholder=\"(optional)\" disabled=\"true\" />";
			
			$searchHTML .= "</div><div class=\"row-fluid\">";
			
			$searchHTML .= "<label class=\"span4\" for=\"inputQueryString_search\">Query</label>";
			$searchHTML .= "<input class=\"span8\" type=\"text\" id=\"inputQueryString_search\" name=\"inputQueryString_search\" placeholder=\"Enter Query String...\" />";
			
			$searchHTML .= "</div><div class=\"row-fluid\">";
			
			$searchHTML .= "<div class=\"form-actions\">";
			$searchHTML .= "<button id=\"searchSubmit\" type=\"submit\" class=\"btn btn-primary pull-right\">";
			$searchHTML .= "<i class=\"icon-search icon-white\"></i>Search";
			$searchHTML .= "</button></div></div></form>";
			
			$searchHTML .= "<script type=\"text/javascript\">$(\"#search\").submit(function(event) {  event.preventDefault(); retrieveDocsByQuery();}); ";
			$searchHTML .= "$(\"#inputTypeSelect_search\").change(function() { if($(this).val()==\"*\"){ $(\"#inputIdQuery_search\").prop('disabled', true); $(\"#inputQueryString_search\").prop('disabled', false); ";
			$searchHTML .= "}else{ $(\"#inputIdQuery_search\").prop('disabled', false); $(\"#inputQueryString_search\").prop('disabled', false); }});</script>";
			
			return "<div id=\"searchHTML\">$searchHTML</div>";
		}else{
			$this->logError("error","empty_array","ESMetadata is Empty, please check the settings entered.");
			return "( empty )";
		}
	}

	public function generateInjectHTML(){
		if(count($this->ESMetadata['indexes'])>0){
			
			$injectHTML = "<form action='index.php' class=\"form-inline\" id=\"inject\">";
			$injectHTML .= "<div class=\"row-fluid\"><label class=\"span4\" for=\"inputIndexSelect_inject\">Index</label>";
			$injectHTML .= "<input type='hidden' id='current_inputIndexSelect_inject' /><select class=\"span8\" name=\"inputIndexSelect_inject\" id=\"inputIndexSelect_inject\">";
			
			$moduleOptions = "";
			$fieldHTML = "";
			$typesAccrossAllIndexes = array();
			$fieldsPerModule = array();
			foreach($this->ESMetadata['indexes'] as $indexName => $metadata){
				$injectHTML .= "<option value=\"$indexName\">$indexName</option>";
				foreach($metadata['modules'] as $moduleName => $fields){
					$typesAccrossAllIndexes[] = $moduleName;
					//build field html:
					$fieldHTML .= "<div id=\"{$indexName}_{$moduleName}_fields\" style='display:none;'>";
					foreach($fields['fields'] as $fieldName=>$value){
						//$value = print_r($value,true);
						/* $value = 
Array
(
    [type] => string
)
						*/
						$fieldHTML .= "<div class=\"row-fluid\">";
						$fieldHTML .= "<label class=\"span4\" for=\"inputField{$fieldName}_inject\">$fieldName</label>";
						$fieldHTML .= "<input class=\"span8\" type=\"text\" id=\"inputField{$fieldName}_inject\" name=\"{$fieldName}\" ".($fieldName=="module"?"disabled='true' value='$moduleName' ":"")."/>";	
						$fieldHTML .= "</div>";
					}
					$fieldHTML .= "</div>";

				}
			}

			$sortedTypes = array_unique($typesAccrossAllIndexes);
			sort($sortedTypes);

			foreach($sortedTypes as $key => $moduleName){
				$moduleOptions .= "<option value=\"$moduleName\">$moduleName</option>";	
			}

			$injectHTML .= "</select></div><div class=\"row-fluid\"><label class=\"span4\" for=\"inputTypeSelect_inject\">Type</label>";
			$injectHTML .= "<select class=\"span8\" name=\"inputTypeSelect_inject\" id=\"inputTypeSelect_inject\"><option value=\"\"></option>";
			$injectHTML .= $moduleOptions;
			$injectHTML .= "</select></div>";

			$injectHTML .= "<div class=\"row-fluid\"><label class=\"span4\" for=\"inputFieldID_inject\">ID</label>";
			$injectHTML .= "<input class=\"span8\" type=\"text\" id=\"inputFieldID_inject\" name=\"inputFieldID_inject\" placeholder=\"(Optional)\" />";
			$injectHTML .= "</div>";

			$injectHTML .= $fieldHTML;
			
			$injectHTML .= "<div class=\"form-actions\">";
			$injectHTML .= "<button id=\"injectSubmit\" type=\"submit\" class=\"btn btn-primary pull-right\">";
			$injectHTML .= "<i class=\"icon-plus icon-white\"></i>Inject Data";
			$injectHTML .= "</button></div></form>";
			
			$injectHTML .= "<script type=\"text/javascript\">$(\"#inject\").submit(function(event) {  event.preventDefault(); injectDoc();}); ";
			$injectHTML .= "
				$(\"#inputTypeSelect_inject\").change(function() { 
					var index = $(\"#inputIndexSelect_inject\").val();
					var type = $(this).val();
					var selectedElement = index + \"_\" + type + \"_fields\";
					
					$(\"#\"+$(\"#current_inputIndexSelect_inject\").val()).hide(\"fast\");
					$(\"#current_inputIndexSelect_inject\").val(selectedElement);
					$(\"#\"+selectedElement).show(\"fast\");
					
				});
				$(\"#inputIndexSelect_inject\").change(function() { 
					$(\"#inputTypeSelect_inject\").val(\"\");
					changeActiveIndex($(this).val());
				});
				</script>
			";
			
			return "<div id=\"injectHTML\">$injectHTML</div>";
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

		// create a new cURL resource
		$ch = curl_init();
		$this->logError("info","curl_url",$this->queryString);
		
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $this->queryString);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		if($method=="XPOST"){
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			//curl_setopt($ch, CURLOPT_POSTFIELDS, $this->jsonQueryString);	
		}elseif($method=="XPUT"){
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->jsonQueryString);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		// grab URL and pass it to the browser
		$results = curl_exec($ch);
		
		$curlError = curl_error($ch);
		if(!empty($curlError)){
			$this->logError("error","curl_error",$curlError);
		}
		
		// close cURL resource, and free up system resources
		curl_close($ch);
		
		$result_array = json_decode($results,TRUE);
		if(count($result_array)>0){
			if(isset($result_array['exists']) && $result_array['exists']==false){
				$this->logError("error","es_error","Elasticsearch did not return a proper dataset.");
				$this->logError("info","curl_results",$results);
			}
		}else{
			$this->logError("error","es_error","Elasticsearch did not return a proper dataset.");
		}
		
		return $result_array;	
	}
}

?>