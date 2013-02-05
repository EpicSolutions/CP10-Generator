<?php
/**************************************************************************************************
 * Connection Settings
 *************************************************************************************************/
$host   = 'localhost';
$user   = 'cp10';
$pass   = 'YGXFbA9hGmhWsHZM';
$dbname = 'cp10';

/**************************************************************************************************
 * Connection Initialization
 *************************************************************************************************/
try {
	$db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);	
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo $e->getMessage();
}
