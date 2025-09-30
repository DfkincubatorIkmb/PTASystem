<?php
$host = 'localhost';
$username = 'root';
$password = ''; 
$dbname = 'tvetikmb_cmspro';

$con = mysqli_connect($host, $username, $password, $dbname);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
