<?php
require_once 'auth_new.php';

// Logout user
logout();

// Redirect to login page
header('Location: login_new.php');
exit;
?>
