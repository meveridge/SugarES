<?php

require_once("ESCall.php");
$connectTest = new ESCall("http://localhost","9201");

$connectTest->connect();

$html = $connectTest->generateTreeHTML();

echo"Start:<br>$html";

?>