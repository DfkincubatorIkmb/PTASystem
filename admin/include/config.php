<?php
$host = 'localhost';
$username = 'tvetikmb';
$password = 'T87d4+E]fe1gMF'; 
$dbname = 'tvetikmb_cmspro';

$con = mysqli_connect($host, $username, $password, $dbname);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
