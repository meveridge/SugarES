<?php

require_once("ESCall.php");
$connectTest = new ESCall("http://localhost","9200","2e675ecc7997f66a90660ba4e3cffb90");

$connectTest->connect();

$docResultsHTML = $connectTest->injectDoc("Accounts","{\"team_set_id\": \"1\",\"module\": \"Calls\",\"doc_owner\": \"1\",\"name\": \"test call from es\"}");

	echo"<div id=\"docResultsContent\">$docResultsHTML</div>";
	
	//populate any error message we have generated 
	//during the process of making calls
	$errorHTML = $connectTest->populateErrorHTML();
	echo"<div id=\"errorContent\">$errorHTML</div>";

?>