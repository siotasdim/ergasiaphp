<?php
session_start();
$page_title = 'Βαθμολογίες';
include('includes/header.php');
require_once('includes/helper_functions.php'); 
require_once('mysqli_connect.php');
print("<h1>Βαθμολογίες</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==2) {

    $q = "SELECT c.title, c.idcourses, sc.idclasses, l.title, n.idusers, u.lastname, u.firstname, n.oralnote, n.testnote, n.average from users u 
                        inner join notes n on n.idusers=u.idusers
                        inner join courses c on c.idcourses=n.idcourses
                        inner join studentsclasses sc on sc.idusers=n.idusers
                        inner join teaches t on (t.idcourses, t.idclasses)=(n.idcourses, sc.idclasses)
                        inner join classes l on l.idclasses=t.idclasses 
                        where t.idusers = ? ";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'i', $id);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $coursetitle, $courseid, $classid, $classtitle, $studentid, $lastname, $firstname, $oralnote, $testnote, $average);

    print("<table>\n"); 
    print("<tr>\n"); 
    print("<th class='hide'>Κωδικός Σειράς</th>\n"); 
    print("<th>Σειρά ΣΠΗΥ</th>\n");
    print("<th class='hide'>Κωδικός Μαθήματος</th>\n"); 
    print("<th>Μάθημα</th>\n"); 
    print("<th class='hide'>Κωδικός Μαθητή</th>\n"); 
    print("<th>Επώνυμο</th>\n"); 
    print("<th>Όνομα</th>\n"); 
    print("<th>Προφορικός Βαθμός</th>\n"); 
    print("<th>Γραπτή Εξέταση</th>\n"); 
    print("<th>Μέσος Όρος</th>\n"); 
    print("<th>Διαγραφή Βαθμολογίας</th>\n"); 
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
        print("<td>$oralnote</td>\n");
        print("<td>$testnote</td>\n");
        print("<td>$average</td>\n");
        print("<td><a href='prof_delete_note.php?studentid=$studentid&courseid=$courseid'>Διαγραφή</a></td>");
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