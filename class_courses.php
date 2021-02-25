<?php
session_start();
$page_title = 'Μαθήματα Σειράς';
include('includes/header.php');
require_once('includes/helper_functions.php'); 
require_once('mysqli_connect.php');
print("<br>");
print("<h1>Μαθήματα Σειράς</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    if (!$classid = filter_input(INPUT_GET, 'classid', FILTER_VALIDATE_INT)) {
        if (!$classid = filter_input(INPUT_POST, 'classid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }
    
    $q = "SELECT l.title, c.idcourses, c.title FROM classescourses cs
            inner join classes l on l.idclasses=cs.idclasses
            inner join courses c on c.idcourses=cs.idcourses
            where l.idclasses=?";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'i', $classid);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $classtitle, $courseid, $coursetitle);

    print("<table>\n"); 
    print("<tr>\n"); 
    print("<th class='hide'>Κωδικός Σειράς</th>\n"); 
    print("<th>Σειρά ΣΠΗΥ</th>\n"); 
    print("<th class='hide'>Κωδικός Μαθήματος</th>\n"); 
    print("<th>Μάθημα</th>\n"); 
    print("</tr>\n"); 

    while(mysqli_stmt_fetch($stmt)) { 
        print("<tr>\n");
        print("<td class='hide'>$classid</td>\n");
        print("<td>$classtitle</td>\n");
        print("<td class='hide'>$courseid</td>\n");
        print("<td>$coursetitle</td>\n");
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