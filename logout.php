<?php
// Logout Script
session_start();

// Destroy session
session_destroy();

// Redirect to home or login
header('Location: /farmers_market/');
exit;
?>
