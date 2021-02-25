<?php
session_start();
$page_title = 'Σειρές';
include('includes/header.php');
require_once('includes/helper_functions.php'); 
require_once('mysqli_connect.php');
print("<br>");
print("<h1>Σειρές</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    $q = "SELECT idclasses, title, startdate, enddate FROM classes";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $classid, $classtitle, $startdate, $enddate);

    print("<table>\n"); 
    print("<tr>\n"); 
    print("<th class='hide'>Κωδικός Σειράς</th>\n"); 
    print("<th>Σειρά ΣΠΗΥ</th>\n"); 
    print("<th>Ημερομηνία Έναρξης</th>\n"); 
    print("<th>Ημερομηνία Λήξης</th>\n"); 
    print("<th>Μαθήματα Σειράς</th>\n");
    print("<th>Μαθητές Σειράς</th>\n");
    print("<th>Επεξεργασία Στοιχείων</th>\n");
    print("<th>Διαγραφή Σειράς</th>\n");
    print("</tr>\n"); 

    while(mysqli_stmt_fetch($stmt)) { 
        print("<tr>\n");
        print("<td class='hide'>$classid</td>\n");
        print("<td>$classtitle</td>\n");
        print("<td>$startdate</td>\n");
        print("<td>$enddate</td>\n");
        print("<td><a href='class_courses.php?classid=$classid'>Μαθήματα</a></td>\n");
        print("<td><a href='class_students.php?classid=$classid'>Μαθητές</a></td>\n");
        print("<td><a href='edit_class.php?classid=$classid'>Επεξεργασία Στοιχείων</a></td>\n");
        print("<td><a href='delete_class.php?classid=$classid'>Διαγραφή</a></td>\n");
        print("</tr>\n");
    }
    print("</table>\n");
    print("<br>\n");
    print("<a href='create_class.php'>Δημιουργία Νέας Σειράς</a>\n");
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);
    include('includes/footer.php');
} else {
    print_access_error_exit();
}
?>