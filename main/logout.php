<?php
session_start(); // Initialize the session

$_SESSION = array(); // Unset all of the session variables

session_destroy(); // Destroy the session.

header("location: ../index.php"); // Redirect to login page
exit;