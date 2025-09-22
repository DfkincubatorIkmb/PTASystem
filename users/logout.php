<?php
session_start();

// Clear semua session
session_unset();
session_destroy();

// Redirect balik ke login page
header("Location: index.php");
exit();