<?php
session_start();
$page_title = 'Μαθήματα';
include('includes/header.php');
require_once('includes/helper_functions.php'); 
require_once('mysqli_connect.php');
print("<br>");
print("<h1>Μαθήματα</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    $q = "SELECT idcourses, title FROM courses";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $courseid, $coursetitle);

    print("<table>\n"); 
    print("<tr>\n");  
    print("<th class='hide'>Κωδικός Μαθήματος</th>\n"); 
    print("<th>Τίτλος Μαθήματος</th>\n"); 
    print("<th>Επεξεργασία Στοιχείων</th>\n");
    print("<th>Ανάθεση σε Σειρά</th>\n");
    print("</tr>\n"); 

    while(mysqli_stmt_fetch($stmt)) { 
        print("<tr>\n");
        print("<td class='hide'>$courseid</td>\n");
        print("<td>$coursetitle</td>\n");
        print("<td><a href='edit_courses.php?courseid=$courseid'>Επεξεργασία Στοιχείων</a></td>\n");
        print("<td><a href='assign_course_to_class.php?courseid=$courseid'>Ανάθεση</a></td>\n");
        print("</tr>\n");
    }
    print("</table>\n");
    print("<br>\n");
    print("<a href='create_course.php'>Δημιουργία Νέου Μαθήματος</a>\n");
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);
    include('includes/footer.php');
} else {
    print_access_error_exit();
}
?>