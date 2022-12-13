<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$db = new SQLite3("/var/db/IdPs.db");
$db->exec("CREATE TABLE idpStatus (
	Idp STRING,
	Time STRING,
	Test STRING,
	TestResult STRING);");
