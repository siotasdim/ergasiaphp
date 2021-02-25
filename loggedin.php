<?php 
require_once('includes/helper_functions.php');
session_start();
check_session();
$page_title = 'Logged In';
require('includes/header.php');
print("<h1>Logged in</h1>\n");
$firstname = $_SESSION['firstname'];
$lastname = $_SESSION['lastname'];
print("<p>Είστε συνδεδεμένος/η $firstname $lastname!</p>\n");
print("<p><a href='logout.php'>Logout</a></p>\n");
include('includes/footer.php');
?>