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
	
}else if($_POST['action']=="retrieveDocsByIndexAndType"){
	require_once("ESCall.php");
	
	$inputServerName = $_POST['inputServerName'];
	$inputPort = $_POST['inputPort'];
	$inputIndex = $_POST['inputIndex'];
	$inputType = $_POST['inputType'];
	
	$ESServer = new ESCall($inputServerName,$inputPort,$inputIndex);
	$docTreeHTML = $ESServer->getDocsByIndexAndType($inputType);
	
	echo"<div id=\"docTreeContent\">$docTreeHTML</div>";
	
	//populate any error message we have generated 
	//during the process of making calls
	$errorHTML = $ESServer->populateErrorHTML();
	echo"<div id=\"errorContent\">$errorHTML</div>";
	
}else if($_POST['action']=="retrieveDocById"){
	require_once("ESCall.php");
	
	$inputServerName = $_POST['inputServerName'];
	$inputPort = $_POST['inputPort'];
	$inputIndex = $_POST['inputIndex'];
	$inputType = $_POST['inputType'];
	$inputId = $_POST['inputId'];
	
	$ESServer = new ESCall($inputServerName,$inputPort,$inputIndex);
	$docHTML = $ESServer->getDocById($inputType,$inputId);
	
	echo"<div id=\"docContent\">$docHTML</div>";
	
	//populate any error message we have generated 
	//during the process of making calls
	$errorHTML = $ESServer->populateErrorHTML();
	echo"<div id=\"errorContent\">$errorHTML</div>";
	
}else{
	echo"No Action Defined.";
}

?>