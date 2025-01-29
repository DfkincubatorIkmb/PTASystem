<?php
define('DB_SERVER','localhost');
define('DB_USER','tvetikmb');
define('DB_PASS' ,'T87d4+E]fe1gMF');
define('DB_NAME', 'tvetikmb_cmspro');
$con = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
// Check connection
if (mysqli_connect_errno())
{
 echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
?>