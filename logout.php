<?php
require_once 'config.php';
// End the session
session_destroy();
// Redirect to the index/login page
header("Location: index.php");
exit();
?>