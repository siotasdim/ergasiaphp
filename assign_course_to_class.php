<?php
session_start();
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');
$page_title = 'Ανάθεση Μαθήματος σε Σειρά';
include('includes/header.php');
print("<br>");
print("<h1>Ανάθεση Μαθήματος σε Σειρά</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    /*$courseid = filter_input(INPUT_GET, 'courseid', FILTER_VALIDATE_INT);*/

    if (!$courseid = filter_input(INPUT_GET, 'courseid', FILTER_VALIDATE_INT)) {
        if (!$courseid = filter_input(INPUT_POST, 'courseid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }
    
    $q = "SELECT idclasses, title FROM classes ";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 0) { 
        print_access_error_exit();
    }
    my_mysqli_stmt_bind_result($stmt, $classid, $classtitle);

    print("<table>\n"); 
    print("<tr>\n");  
    print("<th class='hide'>Κωδικός Μαθήματος</th>\n"); 
    print("<th class='hide'>Κωδικός Σειράς</th>\n"); 
    print("<th>Σειρά ΣΠΗΥ</th>\n"); 
    print("<th>Ανάθεση σε Μαθήματος σε Σειρά</th>\n");
    print("</tr>\n"); 

    while(mysqli_stmt_fetch($stmt)) { 
        print("<tr>\n");
        print("<td class='hide'>$courseid</td>\n");
        print("<td class='hide'>$classid</td>\n");
        print("<td>$classtitle</td>\n");
        print("<td><a href='confirm_course_to_class.php?courseid=$courseid&classid=$classid'>Ανάθεση</a></td>\n");
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