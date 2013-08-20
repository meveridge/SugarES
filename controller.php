<?php

if($_POST['action']=="serverConnection"){
	require_once("ESCall.php");
	
	$inputServerName = $_POST['inputServerName'];
	$inputPort = $_POST['inputPort'];
	$inputIndex = $_POST['inputIndex'];
	
	$ESServer = new ESCall($inputServerName,$inputPort,$inputIndex);

	$ESServer->connect();

	//generate and return the tree html
	$treeHTML = $ESServer->generateTreeHTML();
	echo"<div id=\"treeContent\">$treeHTML</div>";
	
	//generate and return the stats html for the server and all indexes
	$statsHTML = $ESServer->generateStatsHTML();

	echo"<div id=\"statsContent\">$statsHTML</div>";
	
	//populate any error message we have generated 
	//during the process of making calls
	$errorHTML = $ESServer->populateErrorHTML();
	echo"<div id=\"errorContent\">$errorHTML</div>";
	
}else{
	echo"No Action Defined.";
}

?>