<?php
session_start();
$page_title = 'Βαθμολόγηση Μαθητών';
include('includes/header.php');
require_once('includes/helper_functions.php'); 
require_once('mysqli_connect.php');
print("<br>");
print("<h1>Βαθμολόγηση Μαθητών</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==2) {

    if (!$classid = filter_input(INPUT_GET, 'classid', FILTER_VALIDATE_INT)) {
        if (!$classid = filter_input(INPUT_POST, 'classid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }

    if (!$courseid = filter_input(INPUT_GET, 'courseid', FILTER_VALIDATE_INT)) {
        if (!$courseid = filter_input(INPUT_POST, 'courseid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }
//θα πρεπει να βλεπει του ς μαθητες της ταξης ΜΟΝΟ, οχι βαθμους
    $q = "SELECT l.title, c.title, u.idusers, u.lastname, u.firstname
                    from courses c 
                    inner join classescourses cc on cc.idcourses=c.idcourses
                    inner join studentsclasses cs on cs.idclasses=cc.idclasses
                    inner join classes l on l.idclasses=cs.idclasses
                    inner join users u on u.idusers=cs.idusers
                    where l.idclasses = ? and c.idcourses = ? ";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'ii', $classid, $courseid);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $classtitle, $coursetitle, $studentid, $lastname, $firstname);

    print("<table>\n"); 
    print("<tr>\n"); 
    print("<th class='hide'>Κωδικός Σειράς</th>\n"); 
    print("<th>Σειρά ΣΠΗΥ</th>\n"); 
    print("<th class='hide'>Κωδικός Μαθήματος</th>\n"); 
    print("<th>Μάθημα</th>\n"); 
    print("<th class='hide'>Κωδικός Μαθητή</th>\n"); 
    print("<th>Επώνυμο</th>\n"); 
    print("<th>Όνομα</th>\n"); 
    print("<th>Καταχώρηση βαθμολογίας</th>\n"); 
    print("</tr>\n"); 

    while(mysqli_stmt_fetch($stmt)) { 
        print("<tr>\n");
        print("<td class='hide'>$classid</td>\n");
        print("<td>$classtitle</td>\n");
        print("<td class='hide'>$courseid</td>\n");
        print("<td>$coursetitle</td>\n");
        print("<td class='hide'>$studentid</td>\n");
        print("<td>$lastname</td>\n");
        print("<td>$firstname</td>\n");
        print("<td><a href='edit_grades.php?classid=$classid&courseid=$courseid&studentid=$studentid'>Καταχώρηση βαθμολογίας</a></td>\n");
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