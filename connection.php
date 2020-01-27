<?php
$host = "main-database.cmi3ujjdshy2.us-east-2.rds.amazonaws.com";
$port = 3306;
$socket = "";
$user = "admin";
$password = "daniel123qwe";
$dbname = "vventure";

$conn = new mysqli($host, $user, $password, $dbname, $port, $socket)
or die ('');

$conn->set_charset("utf8");

