<?php
session_start();
unset($_SESSION['user_id']);
unset($_SESSION['role_id']);
unset($_SESSION['username']);

session_destroy();



header('location: index.php');

exit;
?>

