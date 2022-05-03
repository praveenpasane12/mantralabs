<?php

$host       = 'localhost'; //or localhost
$database   = 'mantralabs';
$port       = 3306;
$user       = 'root';
$password   = '';

try {
	$conn = new PDO("mysql:host=$host;dbname=$database", $user, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo $e->getMessage();
}

?>