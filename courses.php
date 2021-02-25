<?php
session_start();
$page_title = 'Διδασκαλία';
include('includes/header.php');
require_once('includes/helper_functions.php'); 
require_once('mysqli_connect.php');
print("<h1>Διδασκαλία</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==2) {

    $q = "SELECT c.idcourses, c.title, l.idclasses, l.title from teaches t 
            inner join courses c on c.idcourses=t.idcourses
            inner join classes l on l.idclasses=t.idclasses 
            where t.idusers = ?";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'i', $id);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $courseid, $coursetitle, $classid, $classtitle);

    print("<table>\n"); 
    print("<tr>\n"); 
    print("<th class='hide'>Κωδικός Σειράς</th>\n"); 
    print("<th>Σειρά ΣΠΗΥ</th>\n");
    print("<th class='hide'>Κωδικός Μαθήματος</th>\n"); 
    print("<th>Μάθημα</th>\n"); 
    print("<th>Βαθμολόγηση Μαθητών</th>\n");
    print("</tr>\n"); 

    while(mysqli_stmt_fetch($stmt)) { 
        print("<tr>\n");
        print("<td class='hide'>$classid</td>\n");
        print("<td>$classtitle</td>\n");
        print("<td class='hide'>$courseid</td>\n");
        print("<td>$coursetitle</td>\n");
        print("<td><a href='prof_view_students.php?classid=$classid&courseid=$courseid'>Βαθμολόγηση Μαθητών</a></td>\n");
        print("</tr>\n");
    }
    print("</table>\n");
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);
    include('includes/footer.php');
} else {
    print_access_error_exit();
}
?>