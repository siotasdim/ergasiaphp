<?php
session_start();
$page_title = 'Μαθητές Σειράς';
include('includes/header.php');
require_once('includes/helper_functions.php'); 
require_once('mysqli_connect.php');
print("<br>");
print("<h1>Μαθητές Σειράς</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    if (!$classid = filter_input(INPUT_GET, 'classid', FILTER_VALIDATE_INT)) {
        if (!$classid = filter_input(INPUT_POST, 'classid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }
    
    $q = "SELECT l.title, u.lastname, u.firstname, u.phone, u.email from studentsclasses sc
            inner join users u on u.idusers=sc.idusers
            inner join classes l on l.idclasses=sc.idclasses
            where sc.idclasses = ? ";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'i', $classid);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $classtitle, $lastname, $firstname, $phone, $email);

    print("<h1>$classtitle</h1>\n"); 
    print("<table>\n"); 
    print("<tr>\n"); 
    print("<th>Επώνυμο</th>\n"); 
    print("<th>Όνομα</th>\n"); 
    print("<th>Τηλέφωνο Επικοινωνίας</th>\n"); 
    print("<th>E-mail</th>\n"); 
    print("</tr>\n"); 

    while(mysqli_stmt_fetch($stmt)) { 
        print("<tr>\n");
        print("<td>$lastname</td>\n");
        print("<td>$firstname</td>\n");
        print("<td>$phone</td>\n");
        print("<td>$email</td>\n");
        print("</tr>\n");
    }
    print("</table>\n");
    print("<br>\n");
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);
    include('includes/footer.php');
} else {
    print_access_error_exit();
}
?>