<?php
session_start();
session_unset(); 
session_destroy(); // Destroy session
header("Location: index.html"); // Redirect to the login page
exit;
?>
