<?php
$host = 'localhost';
$db   = 'dds_tunisair'; 
$user = 'root';         
$pass = 'Nvstysh!t123';  
$port = 3306;            

$mysqli = new mysqli($host, $user, $pass, $db, $port);

if ($mysqli->connect_error) {
    die("❌ Database connection failed: " . $mysqli->connect_error);
}
?>
