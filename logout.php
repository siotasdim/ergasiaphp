<?php 
session_start();
require_once('includes/helper_functions.php');
check_session();
$firstname = $_SESSION['firstname'];
$lastname = $_SESSION['lastname'];
$_SESSION = array();
session_destroy();
setcookie('PHPSESSID', '', time()-3600, '/', '', 0, 0);
$page_title = 'Logout';
include('includes/header.php');
print("<h1>Logged Out</h1>\n");
print("<p>Έχετε αποσυνδεθεί $firstname $lastname!</p>\n");
include('includes/footer.php');
?>